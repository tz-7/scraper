<?php

namespace Tz7\WebScraper\WebDriver\SymfonyCrawler;


use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;
use Tz7\WebScraper\Exception\ElementNotFoundException;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;


class CrawlerWebElementFinder
{
    /**
     * @param Crawler                   $crawler
     * @param CrawlerWebElementSelector $selector
     *
     * @return CrawlerWebElementAdapter|null
     *
     * @throws ElementNotFoundException
     */
    public function findElement(Crawler $crawler, CrawlerWebElementSelector $selector)
    {
        $found = $this->findElements($crawler, $selector);

        if (empty($found))
        {
            throw new ElementNotFoundException(
                sprintf('Element not found by "%s"', $selector)
            );
        }

        return array_shift($found);
    }

    /**
     * @param Crawler                   $crawler
     * @param CrawlerWebElementSelector $selector
     *
     * @return CrawlerWebElementAdapter[]
     */
    public function findElements(Crawler $crawler, CrawlerWebElementSelector $selector)
    {
        switch ($selector->getType())
        {
            case WebElementSelectAdapterInterface::TYPE_XPATH:
                $filtered = $crawler->filterXPath($selector->getValue());
                break;

            case WebElementSelectAdapterInterface::TYPE_CSS_SELECTOR:
                $filtered = $crawler->filter($selector->getValue());
                break;

            default:
                throw new InvalidArgumentException('Not supported selector type: ' . $selector->getType());
        }

        $finder = $this;
        $elements = [];

        $filtered->each(
            function (Crawler $element) use ($finder, &$elements)
            {
                $elements[] = new CrawlerWebElementAdapter($element, $finder);
            }
        );

        return $elements;
    }
}
