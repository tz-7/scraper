<?php

namespace Tz7\WebScraper\Browser\Buzz\Factory;


use Buzz\Browser;
use Buzz\Listener\History\Journal;
use Buzz\Listener\HistoryListener;
use Tz7\WebScraper\Browser\Buzz\Listener\CookieListener;
use Tz7\WebScraper\Browser\Buzz\Util\CookieJar;


class BrowserFactory
{
    /** @var ClientFactoryInterface */
    private $clientFactory;

    /**
     * @param ClientFactoryInterface $clientFactory
     */
    public function __construct(ClientFactoryInterface $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param CookieJar $cookieJar
     *
     * @return Browser
     */
    public function createBrowserWithSessionHandling(CookieJar $cookieJar)
    {
        $client          = $this->clientFactory->create();
        $browser         = new Browser($client);
        $cookiesListener = new CookieListener($cookieJar);
        $history         = new Journal();

        $client->setMaxRedirects(0);
        $client->setTimeout(30);
        $browser->addListener($cookiesListener);
        $browser->addListener(new HistoryListener($history));

        return $browser;
    }
}
