<?php

namespace Tz7\WebScraper\Test\WebDriver;


use Doctrine\Common\Cache\ArrayCache;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;


/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ScrapingTestingGroundTest extends AbstractWebDriverTest
{
    public function testLoginWithSuccess()
    {
        /** @var RemoteWebDriver $driver */
        $driver = $this->restoreWebDriver(__CLASS__)->getDriver();
        $driver->get('http://testing-ground.scraping.pro/login');

        $form = $driver->findElement(WebDriverBy::cssSelector('form[action="login?mode=login"]'));
        $form->findElement(WebDriverBy::cssSelector('input[name="usr"]'))->click()->sendKeys('admin');
        $form->findElement(WebDriverBy::cssSelector('input[name="pwd"]'))->click()->sendKeys('12345');
        $form->findElement(WebDriverBy::cssSelector('input[type="submit"]'))->click();

        $result = $driver
            ->findElement(WebDriverBy::cssSelector('#case_login > h3'))
            ->getText();

        $this->assertEquals('WELCOME :)', $result);
    }

    public function testAjax()
    {
        /** @var RemoteWebDriver $driver */
        $driver = $this->restoreWebDriver(__CLASS__)->getDriver();
        $driver->get('http://testing-ground.scraping.pro/ajax');

        $ajaxHtmlListSelector = WebDriverBy::cssSelector('#ajaxHtml > ul');
        $ajaxXmlListSelector = WebDriverBy::cssSelector('#ajaxXml > ul');
        $ajaxJsonListSelector = WebDriverBy::cssSelector('#ajaxJson > ul');
        $ajaxXmlLinkSelector = WebDriverBy::cssSelector('#ajaxXml > a');
        $ajaxJsonLinkSelector = WebDriverBy::cssSelector('#ajaxJson > a');

        $driver->wait(5)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                $ajaxHtmlListSelector
            )
        );
        $this->assertEquals("Tom\nAndrew\nBob", $driver->findElement($ajaxHtmlListSelector)->getText());

        $driver->findElement($ajaxXmlLinkSelector)->click();
        $driver->wait(5)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                $ajaxXmlListSelector
            )
        );
        $this->assertEquals("Justin\nRebecca\nStephen", $driver->findElement($ajaxXmlListSelector)->getText());

        $driver->findElement($ajaxJsonLinkSelector)->click();
        $driver->wait(5)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                $ajaxJsonListSelector
            )
        );
        $this->assertEquals("George\nEric\nAlice", $driver->findElement($ajaxJsonListSelector)->getText());
    }

    public function testUserAgent()
    {
        /** @var RemoteWebDriver $driver */
        $driver = $this->restoreWebDriver(__CLASS__)->getDriver();
        $driver->get('http://testing-ground.scraping.pro/whoami');

        $selector = WebDriverBy::id('USER_AGENT');
        $driver->wait(5)->until(
            WebDriverExpectedCondition::presenceOfElementLocated($selector)
        );

        $this->assertEquals(getenv('WEB_DRIVER_USER_AGENT'), $driver->findElement($selector)->getText());
    }

    public function testHead()
    {
        /** @var RemoteWebDriver $driver */
        $driver = $this->restoreWebDriver(__CLASS__)->getDriver();
        $driver->get('http://testing-ground.scraping.pro/invalid');

        $selector = WebDriverBy::cssSelector('head link[href*=main]');
        $driver->wait(5)->until(
            WebDriverExpectedCondition::presenceOfElementLocated($selector)
        );

        $this->assertContains('/css/main.css', $driver->findElement($selector)->getAttribute('href'));
    }

    /**
     * @return ArrayCache
     */
    protected function getCache()
    {
        return new ArrayCache();
    }

    /**
     * @param string $sessionKey
     *
     * @return WebDriverAdapterInterface
     */
    protected function restoreWebDriver($sessionKey)
    {
        return $this->getSessionManager()->restoreSession($sessionKey, $this->createRemoteWebDriverConfiguration());
    }
}
