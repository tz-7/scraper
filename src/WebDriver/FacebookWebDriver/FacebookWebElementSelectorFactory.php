<?php

namespace Tz7\WebScraper\WebDriver\FacebookWebDriver;


use Facebook\WebDriver\WebDriverBy;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementSelectorFactoryInterface;
use UnexpectedValueException;


class FacebookWebElementSelectorFactory implements WebElementSelectorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createByType($type, $value)
    {
        switch ($type)
        {
            case WebElementSelectAdapterInterface::TYPE_CSS_SELECTOR:
                return new FacebookWebElementSelectAdapter(WebDriverBy::cssSelector($value));

            case WebElementSelectAdapterInterface::TYPE_ID:
                return new FacebookWebElementSelectAdapter(WebDriverBy::id($value));

            case WebElementSelectAdapterInterface::TYPE_NAME:
                return new FacebookWebElementSelectAdapter(WebDriverBy::name($value));

            case WebElementSelectAdapterInterface::TYPE_LINK_TEXT:
                return new FacebookWebElementSelectAdapter(WebDriverBy::linkText($value));

            case WebElementSelectAdapterInterface::TYPE_PARTIAL_LINK_TEXT:
                return new FacebookWebElementSelectAdapter(WebDriverBy::partialLinkText($value));

            case WebElementSelectAdapterInterface::TYPE_TAG_NAME:
                return new FacebookWebElementSelectAdapter(WebDriverBy::tagName($value));

            case WebElementSelectAdapterInterface::TYPE_XPATH:
                return new FacebookWebElementSelectAdapter(WebDriverBy::xpath($value));
        }

        throw new UnexpectedValueException('Unknown selector: ' . $type);
    }
}
