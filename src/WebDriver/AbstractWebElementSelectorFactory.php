<?php

namespace Tz7\WebScraper\WebDriver;


abstract class AbstractWebElementSelectorFactory
{
    /**
     * @param string $selector
     * @param string $default
     *
     * @return WebElementSelectAdapterInterface
     */
    public function create($selector, $default = WebElementSelectAdapterInterface::TYPE_CSS_SELECTOR)
    {
        return $this->createByType(
            $this->getSelectorType($selector, $default),
            $this->getCleanedSelector($selector)
        );
    }

    /**
     * @param string $type
     * @param string $value
     *
     * @return WebElementSelectAdapterInterface
     */
    abstract protected function createByType($type, $value);

    /**
     * @param string $selector
     * @param string $default
     *
     * @return string
     */
    protected function getSelectorType($selector, $default)
    {
        if (($pos = strpos($selector, '::')) === false)
        {
            return $default;
        }

        return substr($selector, 0, $pos);
    }

    /**
     * @param string $selector
     *
     * @return string
     */
    protected function getCleanedSelector($selector)
    {
        if (($pos = strpos($selector, '::')) === false)
        {
            return $selector;
        }

        return (string)substr($selector, $pos + 2);
    }
}
