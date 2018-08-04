<?php

namespace Tz7\WebScraper\Factory;


use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Listener\History\Journal;
use Buzz\Listener\HistoryListener;
use Buzz\Util\CookieJar;
use Tz7\WebScraper\Browser\Buzz\Listener\RedirectedCookieListener;


class BuzzBrowserFactory
{
    /**
     * @param CookieJar $cookieJar
     *
     * @return Browser
     */
    public function createBrowserWithSessionHandling(CookieJar $cookieJar)
    {
        $client          = new Curl();
        $browser         = new Browser($client);
        $cookiesListener = new RedirectedCookieListener($browser, $cookieJar);
        $history         = new Journal();

        $client->setMaxRedirects(0);
        $client->setTimeout(30);
        $browser->addListener($cookiesListener);
        $browser->addListener(new HistoryListener($history));

        return $browser;
    }
}
