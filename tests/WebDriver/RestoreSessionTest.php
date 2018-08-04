<?php

namespace Tz7\WebScraper\Test\WebDriver;


use Doctrine\Common\Cache\ArrayCache;
use Tz7\WebScraper\Request\WebDriverConfiguration;


/**
 * @coversDefaultClass \Tz7\WebScraper\Session\SessionManager
 */
class RestoreSessionTest extends AbstractWebDriverTest
{
    /**
     * @covers ::restoreSession()
     * @dataProvider provideWebDriverConfigurations
     *
     * @param WebDriverConfiguration $webDriverConfiguration
     */
    public function testRestoreSession(WebDriverConfiguration $webDriverConfiguration)
    {
        $sessionKey     = __METHOD__;
        $sessionManager = $this->getSessionManager();

        $expected = $this->getWebDriverFactory()->createWebDriverByConfiguration($webDriverConfiguration);
        $expected->get('https://www.google.com');

        $sessionManager->saveSession($sessionKey, $expected);
        $actual = $sessionManager->restoreSession($sessionKey, $webDriverConfiguration);

        $this->assertEquals($expected->getSessionData(), $actual->getSessionData());
    }

    /**
     * @return ArrayCache
     */
    protected function getCache()
    {
        return new ArrayCache();
    }
}
