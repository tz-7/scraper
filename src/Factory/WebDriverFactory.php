<?php

namespace Tz7\WebScraper\Factory;


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverDimension;
use Tz7\WebScraper\Browser\Buzz\Factory\BrowserFactory;
use Tz7\WebScraper\Browser\Buzz\Util\CookieJar;
use Tz7\WebScraper\Request\WebDriverConfiguration;
use Tz7\WebScraper\WebDriver\FacebookWebDriver\FacebookWebDriverAdapter;
use Tz7\WebScraper\WebDriver\FacebookWebDriver\FacebookWebElementSelectorFactory;
use Tz7\WebScraper\WebDriver\SymfonyCrawler\CrawlerFactory;
use Tz7\WebScraper\WebDriver\SymfonyCrawler\CrawlerWebDriverAdapter;
use Tz7\WebScraper\WebDriver\SymfonyCrawler\CrawlerWebElementFinder as CrawlerWebElementFinder;
use Tz7\WebScraper\WebDriver\SymfonyCrawler\CrawlerWebElementSelectorFactory as CrawlerWebElementSelectorFactory;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;


class WebDriverFactory
{
    /** @var  BrowserFactory */
    private $buzzBrowserFactory;

    /**
     * @param BrowserFactory $buzzBrowserFactory
     */
    public function __construct(BrowserFactory $buzzBrowserFactory)
    {
        $this->buzzBrowserFactory = $buzzBrowserFactory;
    }

    /**
     * @param WebDriverConfiguration $webDriverConfiguration
     * @param mixed                  $sessionData
     *
     * @return WebDriverAdapterInterface
     */
    public function createWebDriverByConfiguration(WebDriverConfiguration $webDriverConfiguration, $sessionData = null)
    {
        if (
            $webDriverConfiguration->getHost() !== null
            && $webDriverConfiguration->getBrowser() !== null
        ) {
            return $this->createRemoteWebDriver($webDriverConfiguration, $sessionData);
        }

        return $this->createCrawlerWebDriver($webDriverConfiguration, $sessionData);
    }

    /**
     * @param WebDriverConfiguration $webDriverConfiguration
     * @param CookieJar|null         $cookieJar
     *
     * @return WebDriverAdapterInterface
     */
    public function createCrawlerWebDriver(WebDriverConfiguration $webDriverConfiguration, CookieJar $cookieJar = null)
    {
        $cookieJar = $cookieJar ?: new CookieJar();

        return new CrawlerWebDriverAdapter(
            $this->buzzBrowserFactory->createBrowserWithSessionHandling($cookieJar),
            $webDriverConfiguration,
            $cookieJar,
            new CrawlerWebElementFinder(),
            new CrawlerFactory(),
            new CrawlerWebElementSelectorFactory()
        );
    }

    /**
     * @param WebDriverConfiguration $webDriverConfiguration
     * @param string                 $sessionId
     *
     * @return WebDriverAdapterInterface
     */
    public function createRemoteWebDriver(WebDriverConfiguration $webDriverConfiguration, $sessionId = null)
    {
        $driver = $sessionId !== null
            ? RemoteWebDriver::createBySessionID($sessionId, $webDriverConfiguration->getHost())
            : RemoteWebDriver::create(
                $webDriverConfiguration->getHost(),
                [
                    WebDriverCapabilityType::BROWSER_NAME => $webDriverConfiguration->getBrowser(),
                    'phantomjs.page.settings.userAgent'   => $webDriverConfiguration->getUserAgent(),
                ],
                $webDriverConfiguration->getTimeout()
            )
        ;

        if (
            $webDriverConfiguration->getWidth() !== null
            && $webDriverConfiguration->getHeight() !== null
        ) {
            $driver->manage()->window()->setSize(
                new WebDriverDimension(
                    $webDriverConfiguration->getWidth(),
                    $webDriverConfiguration->getHeight()
                )
            );
        }

        return new FacebookWebDriverAdapter(
            $driver,
            $webDriverConfiguration,
            new FacebookWebElementSelectorFactory()
        );
    }
}
