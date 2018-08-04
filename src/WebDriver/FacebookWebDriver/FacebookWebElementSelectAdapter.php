<?php

namespace Tz7\WebScraper\WebDriver\FacebookWebDriver;


use Facebook\WebDriver\WebDriverBy;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;


class FacebookWebElementSelectAdapter implements WebElementSelectAdapterInterface
{
    /** @var WebDriverBy */
    private $selector;

    /**
     * @param WebDriverBy $selector
     */
    public function __construct(WebDriverBy $selector)
    {
        $this->selector = $selector;
    }

    /**
     * @return WebDriverBy
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->selector->getMechanism() . ':' . $this->selector->getValue();
    }
}
