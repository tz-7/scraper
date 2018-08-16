<?php

namespace Tz7\WebScraper\WebDriver\SymfonyCrawler;


use InvalidArgumentException;
use Tz7\WebScraper\WebDriver\AbstractWebElementSelectorFactory;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;


class CrawlerWebElementSelectorFactory extends AbstractWebElementSelectorFactory
{
    /**
     * @inheritDoc
     */
    protected function createByType($type, $value)
    {
        $allowedTypes = [
            WebElementSelectAdapterInterface::TYPE_XPATH,
            WebElementSelectAdapterInterface::TYPE_CSS_SELECTOR
        ];

        if (!in_array($type, $allowedTypes, true))
        {
            throw new InvalidArgumentException('Not supported selector type: ' . $type);
        }

        return new CrawlerWebElementSelector($type, $value);
    }
}
