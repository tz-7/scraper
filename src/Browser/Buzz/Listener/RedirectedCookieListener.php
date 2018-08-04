<?php

namespace Tz7\WebScraper\Browser\Buzz\Listener;

use Buzz\Browser;
use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Util\Cookie;
use Buzz\Util\CookieJar;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;


class RedirectedCookieListener implements ListenerInterface
{
    /** @var Browser */
    private $browser;

    /** @var CookieJar */
    private $cookieJar;

    /**
     * @param Browser   $browser
     * @param CookieJar $cookieJar
     */
    public function __construct(Browser $browser, CookieJar $cookieJar)
    {
        $this->browser   = $browser;
        $this->cookieJar = $cookieJar;
    }

    public function setCookies($cookies)
    {
        $this->cookieJar->setCookies($cookies);
    }

    public function getCookies()
    {
        return $this->cookieJar->getCookies();
    }

    /**
     * Adds a cookie to the current cookie jar.
     *
     * @param Cookie $cookie A cookie object
     */
    public function addCookie(Cookie $cookie)
    {
        $this->cookieJar->addCookie($cookie);
    }

    /**
     * @inheritdoc
     */
    public function preSend(RequestInterface $request)
    {
        $this->cookieJar->clearExpiredCookies();
        $this->cookieJar->addCookieHeaders($request);
    }

    /**
     * @inheritdoc
     */
    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        $this->cookieJar->processSetCookieHeaders($request, $response);

        $location = $response->getHeader('Location');
        if ($location !== null)
        {
            $redirected = $this->browser->get(
                UriResolver::resolve(new Uri($request->getHost()), new Uri($location)),
                $request->getHeaders()
            );

            $this->cookieJar->processSetCookieHeaders($request, $response);

            $response->setHeaders($redirected->getHeaders());
            $response->setContent($redirected->getContent());
        }
    }
}
