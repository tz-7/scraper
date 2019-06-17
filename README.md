# TZ-7 Web Scraper

**Just for fun project!** 

This library can be used to do things on web pages and 
 [scrape](https://en.wikipedia.org/wiki/Web_scraping) the result.

The main concept behind is to decouple the actions which describe what needs to be done
 and what to scrape, and decouple the execution of the parsed commands
 from the executor web client or driver.
 
As a result of this,
* the scraping can be changed without the need to change the application
* the driver can be switched from [Crawler](https://symfony.com/doc/current/components/dom_crawler.html)
 to [phantomjs](http://phantomjs.org/quick-start.html) when needed.
* a new web driver can be added by implementing adapters 

## Current status

* Adapted for web drivers:
    * [Facebook's WebDriver](https://github.com/facebook/php-webdriver)
        * Client (tested): [phantomjs](http://phantomjs.org/quick-start.html)  
          _Resolution and rendering is buggy sometimes :)_
    * [Symfony's Crawler](https://symfony.com/doc/current/components/dom_crawler.html)
        * Client: [Buzz\Browser](https://github.com/kriswallsmith/Buzz)
* Alter Scrape without changing php code
    * Configurable from database, yaml, or any array type
* Recursive control-flow.

## Configuration

The examples below are in YAML.

### About Commands

Commands are atomic level instructions for the Scraper. Each command does exactly one thing. 

    prepared_by:
        {$command to execute before}
        
    command:     string      # result of Handler.getName()
    propogate:   bool        # clones context if set to false
    sleep_after: int         # sleep after command execution to prevent flooding sites
    url_context: string|null # url pattern for detecting redirections 
    on_redirect:
        {$command to execute if url changes}
        
    {command specified settings}
    
    processed_by:
        {$command to execute after}
            
### Predefined Commands

#### Navigate

The first thing to do is to open a web page in the "Browser".

    command: navigate
    url:     string      # expression language definition

#### Click

Buttons, can be clicked when using a remote web driver, like selenium.

    command:  click
    selector: string     # selector

#### Submit form

Search, login, etc forms can be submitted.

    command: form_submit
    form:    string      # selector for the <form> DOM
    submit:  string      # selector for the type="submit" button
    fields:
        {key-value pairs to fill}
    evaluate:
        {key-expression pairs to evaluate and fill}
    optional:
        {list keys which can be removed if value equals null or empty string}
            
#### Read text

We often need to read the text of a DOM.

    command:  read_text
    selector: string     # selector for the element DOM to read
        
#### Read attribute

Sometimes there are hidden data in the DOM attributes if we run without JavaScript.

    command:   read_attribute
    selector:  string          # selector for the element DOM which has the attribute
    attribute: string          # name of the attribute to read

#### Evaluate element

Access the element DOM thru [WebElementAdapterInterface](src/WebDriver/WebElementAdapterInterface.php)
 and use it in an expression as "element".
 
    command:    evaluate_element
    expression: string             # expression language definition

#### Command sequence

Run multiple commands on a page and associate the results under specified keys.

    command:   command_sequence
    sequence:
        {key-command pairs to execute}
        
#### Element sequence

Run commands on each selected element and store the result into a sequenced array.
 
    command: element_sequence
    on_each:
        {command to execute on each element}
        
#### Map element

Create a key-value pair of an element, like anchor text and link.

    command: map_element
    key:
        {command to create key, the node must seed string}
    value:
        {command to create value}
        
#### Seed expression context

Commands are nodes of a tree, and their results are seeds. 
 These seeds can be added to the context of the expression language.
 
    command: seed_expression_context
    key:     string                    # key of context
    
### Example

    command: navigate
    url: '"http://testing-ground.scraping.pro/login"'
    url_context: '\/login'
    
    processed_by:
        command: form_submit
        form: 'form[action="login?mode=login"]'
        submit: 'input[type="submit"]'
        fields:
            usr: admin
            pwd: 12345
            
        processed_by:
            command: read_text
            selector: #case_login > h3
            
See more [here](tests/Scraper).


### Misc.

#### Expression Language

[Read here](https://symfony.com/doc/current/components/expression_language.html)

#### Selector

[Chat sheet](https://devhints.io/xpath)

* CSS: default
* XPATH: prefix with "xpath::" (double colon is important), eg: "xpath:://a[contains(@href, "download.php")]"

## Installation

1. composer.json

        "repositories": [
            {
                "type": "vcs",
                "url":  "git@github.com:tz-7/scraper.git"
            }
        ],
        
2. master under development

        composer install tz7/scraper:dev-master@dev
        
3. Create a scraper instance and run a config

```php
<?php

use League\Tactician\CommandBus;
use League\Tactician\Handler\Locator\InMemoryLocator;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Middleware\CommandHandlerMiddleware;
use Tz7\WebScraper\Command\Middleware\NullElementMiddleware;
use Tz7\WebScraper\Command\Middleware\RedirectCheckMiddleware;
use Tz7\WebScraper\Command\Middleware\TreeGrowthMiddleware;
use Tz7\WebScraper\ExpressionLanguage\ExpressionLanguageProvider;
use Tz7\WebScraper\Factory\HandlerCollectionFactory;
use Tz7\WebScraper\Normalizer\SeedNormalizer;
use Tz7\WebScraper\Scraper;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;

/** @var LoggerInterface $logger */
/** @var WebDriverAdapterInterface $webDriverAdapter */
/** @var array $config */
/** @var array $expressionContext */

$expressionLanguageProvider = new ExpressionLanguageProvider();

$collectionFactory = new HandlerCollectionFactory(
    $logger,
    new ExpressionLanguage(
        null,
        [
            $expressionLanguageProvider
        ]
    )
);

$locator = new InMemoryLocator();

foreach ($collectionFactory->getCommands() as $handler)
{
    $locator->addHandler($handler, $handler->getName());
}

$commandBus = new CommandBus(
    [
        new TreeGrowthMiddleware(new SeedNormalizer()),
        new RedirectCheckMiddleware(),
        new NullElementMiddleware(),
        new CommandHandlerMiddleware($locator)
    ]
);

$scraper = new Scraper(
    $commandBus,
    $webDriverAdapter,
    $logger,
    $expressionLanguageProvider
);

$scraper->scrape(
    $config,
    $expressionContext
);
```


## Future plans

* Test Facebook's web driver with Selenium Chrome/Firefox.
* Test continue scraping an already opened tab.

## Notes

Gave up on Deferred/Promise based tree-walk on commands after a half year with full of headache, so the 
 [TreeGrowthMiddleware](src/Command/Middleware/TreeGrowthMiddleware.php) speaks for itself :D
 
I'm using it to submit search forms and extract data from sites, where there is no RSS or API.