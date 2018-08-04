<?php

namespace Tz7\WebScraper\WebDriver\SymfonyCrawler;


use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;
use Tz7\WebScraper\WebDriver\WebElementAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;


class CrawlerWebElementAdapter implements WebElementAdapterInterface
{
    /** @var Crawler */
    private $element;

    /** @var CrawlerWebElementFinder */
    private $finder;

    /**
     * @param Crawler                 $element
     * @param CrawlerWebElementFinder $finder
     */
    public function __construct(Crawler $element, CrawlerWebElementFinder $finder)
    {
        $this->element = $element;
        $this->finder  = $finder;
    }

    /**
     * @inheritDoc
     */
    public function click()
    {
        $nodeName = $this->element->nodeName();
        
        // TODO: Implement click() method.

        throw new RuntimeException(sprintf('Click on "%s" is not implemented yet!', $nodeName));
    }

    /**
     * @inheritDoc
     */
    public function getTagName()
    {
        return $this->element->nodeName();
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($attributeName)
    {
        return $this->element->attr($attributeName);
    }

    /**
     * @inheritDoc
     */
    public function getText()
    {
        return $this->element->text();
    }

    /**
     * @inheritDoc
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @inheritDoc
     */
    public function findElement(WebElementSelectAdapterInterface $selector)
    {
        return $this->finder->findElement($this->element, $selector->getSelector());
    }

    /**
     * @inheritDoc
     */
    public function findElements(WebElementSelectAdapterInterface $selector)
    {
        return $this->finder->findElements($this->element, $selector->getSelector());
    }
}
