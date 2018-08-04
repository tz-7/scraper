<?php

namespace Tz7\WebScraper\WebDriver\FacebookWebDriver;


use Facebook\WebDriver\Remote\RemoteWebElement;
use Tz7\WebScraper\Exception\ElementNotFoundException;
use Tz7\WebScraper\WebDriver\WebElementAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;


class FacebookWebElementAdapter implements WebElementAdapterInterface
{
    /** @var RemoteWebElement */
    private $element;

    /**
     * @param RemoteWebElement $element
     */
    public function __construct(RemoteWebElement $element)
    {
        $this->element = $element;
    }

    /**
     * @inheritDoc
     */
    public function click()
    {
        $this->element = $this->element->click();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTagName()
    {
        return $this->element->getTagName();
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($attributeName)
    {
        return $this->element->getAttribute($attributeName);
    }

    /**
     * @inheritDoc
     */
    public function getText()
    {
        return $this->element->getText();
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
        $element = $this->element->findElement($selector->getSelector());

        if ($element instanceof RemoteWebElement)
        {
            return new static($element);
        }

        throw new ElementNotFoundException(
            sprintf('Element not found by "%s"', $selector)
        );
    }

    /**
     * @inheritDoc
     */
    public function findElements(WebElementSelectAdapterInterface $selector)
    {
        return array_map(
            function (RemoteWebElement $element)
            {
                return new static($element);
            },
            $this->element->findElements($selector->getSelector())
        );
    }
}
