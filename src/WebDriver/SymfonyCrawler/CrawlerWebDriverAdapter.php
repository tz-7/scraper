<?php

namespace Tz7\WebScraper\WebDriver\SymfonyCrawler;


use Buzz\Browser;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\Response;
use Buzz\Util\CookieJar;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Symfony\Component\DomCrawler\Crawler;
use Tz7\WebScraper\Request\WebDriverConfiguration;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;
use UnexpectedValueException;


class CrawlerWebDriverAdapter implements WebDriverAdapterInterface
{
    const MAX_REDIRECTION = 2;

    /** @var Browser */
    private $browser;

    /** @var WebDriverConfiguration */
    private $configuration;

    /** @var CookieJar */
    private $cookieJar;

    /** @var Crawler */
    private $crawler;

    /** @var CrawlerWebElementFinder */
    private $finder;

    /** @var CrawlerFactory */
    private $crawlerFactory;

    /** @var CrawlerWebElementSelectorFactory */
    private $selectorFactory;

    /** @var string */
    private $source;

    /**
     * @param Browser                          $browser
     * @param WebDriverConfiguration           $configuration
     * @param CookieJar                        $cookieJar
     * @param CrawlerWebElementFinder          $finder
     * @param CrawlerFactory                   $crawlerFactory
     * @param CrawlerWebElementSelectorFactory $selectorFactory
     */
    public function __construct(
        Browser $browser,
        WebDriverConfiguration $configuration,
        CookieJar $cookieJar,
        CrawlerWebElementFinder $finder,
        CrawlerFactory $crawlerFactory,
        CrawlerWebElementSelectorFactory $selectorFactory
    ) {
        $this->browser         = $browser;
        $this->configuration   = $configuration;
        $this->cookieJar       = $cookieJar;
        $this->finder          = $finder;
        $this->crawlerFactory  = $crawlerFactory;
        $this->selectorFactory = $selectorFactory;
    }

    /**
     * @inheritDoc
     */
    public function get($url)
    {
        $response = $this->browserCall($url, RequestInterface::METHOD_GET);

        $this->setUpCrawler($response, $url);

        return new CrawlerWebElementAdapter($this->crawler, $this->finder);
    }

    /**
     * @inheritDoc
     */
    public function download($url)
    {
        return $this->browserCall($url, RequestInterface::METHOD_GET)->getContent();
    }

    /**
     * @inheritDoc
     */
    public function submit(
        WebElementAdapterInterface $formAdapter,
        WebElementAdapterInterface $buttonAdapter = null,
        array $fields = null
    ) {
        /** @var Crawler $formCrawler */
        $formCrawler = $formAdapter->getElement();
        $method      = strtoupper($formCrawler->attr('method') ?: 'GET');
        $action      = $formCrawler->attr('action');
        $form        = $formCrawler->form($fields, $method);
        $content     = http_build_query($form->getValues());

        if ($method === 'GET')
        {
            return $this->get($this->getAbsoluteUrl($action, $content));
        }

        if ($method !== 'POST')
        {
            throw new UnexpectedValueException('Form with method ' . $method . ' was not expected.');
        }

        $url      = $this->getAbsoluteUrl($action);
        $response = $this->browserCall($url, RequestInterface::METHOD_POST, $content);

        $this->setUpCrawler($response, $url);

        return new CrawlerWebElementAdapter($this->crawler, $this->finder);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentURL()
    {
        return $this->crawler ? $this->crawler->getUri() : null;
    }

    /**
     * @inheritDoc
     */
    public function getDriver()
    {
        return $this->crawler;
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
        return $this->cookieJar;
    }

    /**
     * @inheritDoc
     */
    public function findElement(WebElementSelectAdapterInterface $selector)
    {
        return $this->finder->findElement($this->crawler, $selector->getSelector());
    }

    /**
     * @inheritDoc
     */
    public function findElements(WebElementSelectAdapterInterface $selector)
    {
        return $this->finder->findElements($this->crawler, $selector->getSelector());
    }

    /**
     * @param string $path
     * @param string $query
     *
     * @return string
     */
    private function getAbsoluteUrl($path, $query = null)
    {
        $base = new Uri($this->crawler->getBaseHref());
        $uri  = new Uri($path);

        if ($query !== null)
        {
            $uri = $uri->withQuery($query);
        }

        return (string) UriResolver::resolve($base, $uri);
    }

    /**
     * @param string $url
     * @param string $method
     * @param string $content
     * @param int    $redirect
     *
     * @return MessageInterface
     */
    private function browserCall(&$url, $method, $content = '', $redirect = self::MAX_REDIRECTION)
    {
        /** @var Response $response */
        $response = $this->browser->call($url, $method, $this->getHeaders($url), $content);

        $location = $response->getHeader('Location');
        if ($location !== null && $redirect > 0)
        {
            $statusCode = (int)$response->getStatusCode();
            $url = UriResolver::resolve(new Uri($url), new Uri($location));

            $method = in_array($statusCode, [307, 308], true) ? $method : RequestInterface::METHOD_GET;

            return $this->browserCall($url, $method, '', $redirect - 1);
        }

        return $response;
    }

    /**
     * @param MessageInterface $message
     * @param string           $url
     */
    private function setUpCrawler(MessageInterface $message, $url)
    {
        $body = $message->getContent();

        foreach (explode(', ', $message->getHeader('Content-Encoding')) as $coding)
        {
            if ($coding === 'gzip')
            {
                $body = gzdecode($body);
            }
            elseif ($coding === 'deflate')
            {
                $body = http_inflate($body);
            }
        }

        $this->source  = $body;
        $this->crawler = $this->crawlerFactory->create($this->source, $url);
    }

    /**
     * @param string $url
     *
     * @return array
     */
    private function getHeaders($url)
    {
        $headers = [
            'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Encoding'           => 'gzip, deflate',
            'Accept-Language'           => 'hu-HU,hu;q=0.9,en-US;q=0.8,en;q=0.7',
            'Connection'                => 'keep-alive',
            'DNT'                       => 1,
            'Host'                      => parse_url($url, PHP_URL_HOST),
            'Upgrade-Insecure-Requests' => 1,
            'User-Agent'                => $this->configuration->getUserAgent()
        ];

        if ($this->crawler)
        {
            $headers['Referer'] = $this->crawler->getUri();
        }

        return $headers;
    }
}
