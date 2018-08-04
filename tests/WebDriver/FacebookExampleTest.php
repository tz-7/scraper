<?php

namespace Tz7\WebScraper\Test\WebDriver;


use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;


class FacebookExampleTest extends AbstractWebDriverTest
{
    public function testExample()
    {
        $webDriverConfiguration = $this->createRemoteWebDriverConfiguration();

        $driver = RemoteWebDriver::create(
            $webDriverConfiguration->getHost(),
            [
                WebDriverCapabilityType::BROWSER_NAME => $webDriverConfiguration->getBrowser(),
                'phantomjs.page.settings.userAgent'   => $webDriverConfiguration->getUserAgent(),
            ],
            $webDriverConfiguration->getTimeout()
        );

        $driver->manage()->window()->setSize(
            new WebDriverDimension(
                $webDriverConfiguration->getWidth(),
                $webDriverConfiguration->getHeight()
            )
        );
        
        $driver->get('http://www.seleniumhq.org/');
        $driver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('menu_about'))
        );

        $this->assertEquals($driver->getTitle(), 'Selenium - Web Browser Automation');

        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        $cookies = $driver->manage()->getCookies();

        $this->assertNotEmpty($cookies);

        $driver->findElement(WebDriverBy::cssSelector('#menu_about > a'))->click();
        $driver->wait()->until(
            WebDriverExpectedCondition::titleContains('About')
        );

        $this->assertEquals('About Selenium', $driver->getTitle());

        $driver->findElement(WebDriverBy::id('q'))->sendKeys('php');
        $driver->findElement(WebDriverBy::id('submit'))->click();
        $driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                WebDriverBy::className('gsc-result')
            )
        );

        $this->assertTrue(true);
    }
}
