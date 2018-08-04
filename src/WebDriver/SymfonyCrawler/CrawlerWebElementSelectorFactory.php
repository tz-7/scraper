<?php

namespace Tz7\WebScraper\WebDriver\SymfonyCrawler;


use InvalidArgumentException;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementSelectorFactoryInterface;


class CrawlerWebElementSelectorFactory implements WebElementSelectorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createByType($type, $value)
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
