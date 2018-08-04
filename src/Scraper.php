<?php

namespace Tz7\WebScraper;


use ArrayObject;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\ExpressionLanguage\ExpressionLanguageProvider;
use Tz7\WebScraper\Request\Context;
use Tz7\WebScraper\Request\ElementStack;
use Tz7\WebScraper\Request\History;
use Tz7\WebScraper\Response\Seed;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;


class Scraper
{
    /** @var CommandBus */
    private $commandBus;

    /** @var WebDriverAdapterInterface */
    private $driver;

    /** @var LoggerInterface */
    private $logger;

    /** @var ExpressionLanguage */
    private $language;

    /**
     * @param CommandBus                          $commandBus
     * @param WebDriverAdapterInterface           $driver
     * @param LoggerInterface                     $logger
     * @param ExpressionFunctionProviderInterface $expressionFunctionProvider
     */
    public function __construct(
        CommandBus $commandBus,
        WebDriverAdapterInterface $driver,
        LoggerInterface $logger = null,
        ExpressionFunctionProviderInterface $expressionFunctionProvider = null
    ) {
        $this->commandBus = $commandBus;
        $this->driver     = $driver;
        $this->logger     = $logger ?: new NullLogger();
        $this->language   = new ExpressionLanguage();
        $this->language->registerProvider($expressionFunctionProvider ?: new ExpressionLanguageProvider());
    }

    /**
     * @return WebDriverAdapterInterface
     */
    public function getWebDriver()
    {
        return $this->driver;
    }

    /**
     * @param array $config
     * @param array $expressionContext
     *
     * @return Seed
     */
    public function scrape(array $config, $expressionContext = [])
    {
        $expressionContextObject = new ArrayObject($expressionContext);
        $expressionContextObject->offsetSet('scraper', $this);
        $expressionContextObject->offsetSet('driver', $this->driver);

        $context = new Context($this->driver, '', $expressionContextObject, $config);

        $command = new Command($context, new History([], new ElementStack()));

        $this->logger->info('Scraper: begin');

        $this->commandBus->handle($command);

        return $command->getSeed();
    }

    /**
     * @param string $url
     *
     * @return bool|string
     */
    public function download($url)
    {
        return $this->driver->download($url);
    }
}
