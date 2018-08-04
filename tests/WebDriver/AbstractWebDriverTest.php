<?php

namespace Tz7\WebScraper\Test\WebDriver;


use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\VoidCache;
use PHPUnit\Framework\TestCase;
use Tz7\WebScraper\Browser\PhantomJsRunner;
use Tz7\WebScraper\Factory\BuzzBrowserFactory;
use Tz7\WebScraper\Factory\WebDriverFactory;
use Tz7\WebScraper\Request\WebDriverConfiguration;
use Tz7\WebScraper\Session\SessionManager;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;


abstract class AbstractWebDriverTest extends TestCase
{
    /** @var WebDriverFactory */
    private $webDriverFactory;

    /** @var SessionManager */
    private $sessionManager;

    /**
     * @return array
     */
    public function provideWebDriverConfigurations()
    {
        return [
            'remoteWebDriverConfiguration' => [$this->createRemoteWebDriverConfiguration()],
            'localWebDriverConfiguration'  => [$this->createLocalWebDriverConfiguration()]
        ];
    }

    /**
     * @return array
     */
    public function provideWebDrivers()
    {
        return [
            'remoteWebDriverAdapter' => [
                $this->getSessionManager()->restoreSession(
                    static::class,
                    $this->createRemoteWebDriverConfiguration()
                )
            ],
            'localWebDriverAdapter'  => [
                $this->getSessionManager()->restoreSession(
                    static::class,
                    $this->createLocalWebDriverConfiguration()
                )
            ]
        ];
    }

    /**
     * @return WebDriverConfiguration
     */
    protected function createLocalWebDriverConfiguration()
    {
        return new WebDriverConfiguration(
            getenv('WEB_DRIVER_USER_AGENT'),
            getenv('WEB_DRIVER_TIMEOUT')
        );
    }

    /**
     * @return WebDriverConfiguration
     */
    protected function createRemoteWebDriverConfiguration()
    {
        $host = getenv('WEB_DRIVER_HOST');

        $phantomJsRunner = new PhantomJsRunner($host);
        if (!$phantomJsRunner->isRunning())
        {
            printf("WebKit is not running, please wait...");

            $phantomJsRunner->run();
        }

        return new WebDriverConfiguration(
            getenv('WEB_DRIVER_USER_AGENT'),
            getenv('WEB_DRIVER_TIMEOUT'),
            getenv('WEB_DRIVER_BROWSER'),
            $host,
            getenv('WEB_DRIVER_WIDTH'),
            getenv('WEB_DRIVER_HEIGHT')
        );
    }

    /**
     * @return WebDriverFactory
     */
    protected function getWebDriverFactory()
    {
        if ($this->webDriverFactory === null)
        {
            $this->webDriverFactory = new WebDriverFactory(new BuzzBrowserFactory());
        }

        return $this->webDriverFactory;
    }

    /**
     * @return SessionManager
     */
    protected function getSessionManager()
    {
        if ($this->sessionManager === null)
        {
            $this->sessionManager = new SessionManager($this->getWebDriverFactory(), $this->getCache());
        }

        return $this->sessionManager;
    }

    /**
     * @param string                    $sessionKey
     * @param WebDriverAdapterInterface $driver
     */
    protected function saveWebDriver($sessionKey, WebDriverAdapterInterface $driver)
    {
        $this->getSessionManager()->saveSession($sessionKey, $driver);
    }

    /**
     * @return Cache
     */
    protected function getCache()
    {
        return new VoidCache();
    }
}
