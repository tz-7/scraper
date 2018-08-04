<?php

namespace Tz7\WebScraper\WebDriver\SymfonyCrawler;


use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;


class CrawlerWebElementSelector implements WebElementSelectAdapterInterface
{
    /** @var string */
    private $type;

    /** @var string */
    private $value;

    /**
     * @param string $type
     * @param string $value
     */
    public function __construct($type, $value)
    {
        $this->type  = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getSelector()
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->type . ':' . $this->value;
    }
}
