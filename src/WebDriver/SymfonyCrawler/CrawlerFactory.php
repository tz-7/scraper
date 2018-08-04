<?php

namespace Tz7\WebScraper\WebDriver\SymfonyCrawler;


use GuzzleHttp\Psr7\Uri;
use Symfony\Component\DomCrawler\Crawler;


class CrawlerFactory
{
    /**
     * @param string $html
     * @param string $url
     *
     * @return Crawler
     */
    public function create($html, $url)
    {
        $uri = new Uri($url);

        return new Crawler($html, (string)$uri, $this->composeBaseUrl($uri));
    }

    /**
     * @param Uri $uri
     *
     * @return string
     */
    private function composeBaseUrl(Uri $uri)
    {
        return Uri::composeComponents(
            $uri->getScheme(),
            $uri->getAuthority(),
            '',
            '',
            ''
        );
    }
}
