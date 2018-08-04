<?php

namespace Tz7\WebScraper\WebDriver\FacebookWebDriver;


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Tz7\WebScraper\Exception\ElementNotFoundException;
use Tz7\WebScraper\Request\WebDriverConfiguration;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;
use UnexpectedValueException;


class FacebookWebDriverAdapter implements WebDriverAdapterInterface
{
    /** @var RemoteWebDriver */
    private $driver;

    /** @var WebDriverConfiguration */
    private $webDriverConfiguration;

    /** @var FacebookWebElementSelectorFactory */
    private $selectorFactory;

    /**
     * @param RemoteWebDriver                   $driver
     * @param WebDriverConfiguration            $webDriverConfiguration
     * @param FacebookWebElementSelectorFactory $selectorFactory
     */
    public function __construct(
        RemoteWebDriver $driver,
        WebDriverConfiguration $webDriverConfiguration,
        FacebookWebElementSelectorFactory $selectorFactory
    ) {
        $this->driver                 = $driver;
        $this->webDriverConfiguration = $webDriverConfiguration;
        $this->selectorFactory        = $selectorFactory;
    }

    /**
     * @inheritDoc
     */
    public function get($url)
    {
        $this->driver->get($url);

        return $this->getRootNode();
    }

    /**
     * @inheritDoc
     */
    public function download($url)
    {
        $cookies = $this->getCookies();
        $header  = [
            'Accept-Language' => 'en-US;q=0.9,en;q=0.8',
            'User-Agent'      => $this->webDriverConfiguration->getUserAgent(),
            'Cookie'          => implode(
                '; ',
                array_map(
                    function ($key, $value)
                    {
                        return $key . '=' . $value;
                    },
                    array_keys($cookies),
                    array_values($cookies)
                )
            )
        ];

        $context = stream_context_create(
            [
                'http' => [
                    'method' => "GET",
                    'header' => $header
                ]
            ]
        );

        return file_get_contents($url, false, $context);
    }

    /**
     * @inheritDoc
     */
    public function submit(
        WebElementAdapterInterface $formAdapter,
        WebElementAdapterInterface $buttonAdapter = null,
        array $fields = null
    ) {
        if ($fields !== null)
        {
            $this->fillForm($formAdapter, $fields);
        }

        if ($buttonAdapter !== null)
        {
            $this->submitInput($buttonAdapter);
        }
        else
        {
            throw new UnexpectedValueException('Submit button must be specified to submit with RemoteWebDriver.');
        }

        return $this->getRootNode();
    }

    /**
     * @inheritDoc
     */
    public function getCurrentURL()
    {
        return $this->driver->getCurrentURL();
    }

    /**
     * @return RemoteWebDriver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    public function getSelectorFactory()
    {
        return $this->selectorFactory;
    }

    /**
     * @inheritDoc
     */
    public function getSessionData()
    {
        return $this->driver->getSessionID();
    }

    /**
     * @inheritDoc
     */
    public function findElement(WebElementSelectAdapterInterface $selector)
    {
        $element = $this->driver->findElement($selector->getSelector());

        if ($element instanceof RemoteWebElement)
        {
            return new FacebookWebElementAdapter($element);
        }

        throw new ElementNotFoundException(
            sprintf('Element not found by "%s"', $selector)
        );
    }

    /**
     * @inheritDoc
     */
    public function findElements(WebElementSelectAdapterInterface $selector)
    {
        return array_map(
            function (RemoteWebElement $element)
            {
                return new FacebookWebElementAdapter($element);
            },
            $this->driver->findElements($selector->getSelector())
        );
    }

    /**
     * @param WebElementAdapterInterface $formAdapter
     * @param array                      $fields
     */
    private function fillForm(WebElementAdapterInterface $formAdapter, array $fields)
    {
        $form = $formAdapter->getElement();
        if (!$form instanceof RemoteWebElement)
        {
            throw new UnexpectedValueException(
                sprintf(
                    'Decorated element class expected to be "%s", got "%s"',
                    RemoteWebElement::class,
                    get_class($form)
                )
            );
        }

        foreach ($fields as $key => $value)
        {
            $form
                ->findElement(WebDriverBy::name($key))
                ->click()
                ->sendKeys($value);
        }
    }

    /**
     * @param WebElementAdapterInterface $buttonAdapter
     */
    private function submitInput(WebElementAdapterInterface $buttonAdapter)
    {
        $button = $buttonAdapter->getElement();
        if (!$button instanceof RemoteWebElement)
        {
            throw new UnexpectedValueException(
                sprintf(
                    'Decorated element class expected to be "%s", got "%s"',
                    RemoteWebElement::class,
                    get_class($button)
                )
            );
        }

        $button->submit();
    }

    /**
     * @return WebElementAdapterInterface
     *
     * @throws ElementNotFoundException
     */
    private function getRootNode()
    {
        $rootSelector = WebDriverBy::xpath('/*');
        $this->driver->wait(10)->until(WebDriverExpectedCondition::presenceOfElementLocated($rootSelector));

        $root = $this->findElement(new FacebookWebElementSelectAdapter($rootSelector));

        if ($root === null)
        {
            throw new ElementNotFoundException('Root not found after submit.');
        }

        return $root;
    }

    /**
     * @inheritDoc
     */
    private function getCookies()
    {
        $cookies = [];

        foreach ($this->driver->manage()->getCookies() as $cookie)
        {
            $cookies[$cookie->getName()] = $cookie->getValue();
        }

        return $cookies;
    }
}
