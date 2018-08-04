<?php

namespace Tz7\WebScraper\Request;


use Tz7\WebScraper\WebDriver\WebElementAdapterInterface;


class ElementStack
{
    /** @var WebElementAdapterInterface[] */
    private $storage = [];

    /**
     * @param WebElementAdapterInterface $element
     *
     * @return $this
     */
    public function append(WebElementAdapterInterface $element)
    {
        $this->storage[] = $element;

        return $this;
    }

    /**
     * @param WebElementAdapterInterface $element
     *
     * @return $this
     */
    public function reset(WebElementAdapterInterface $element)
    {
        $this->storage = [$element];

        return $this;
    }

    /**
     * @return WebElementAdapterInterface
     */
    public function pop()
    {
        return array_pop($this->storage);
    }

    /**
     * @return WebElementAdapterInterface|false
     */
    public function top()
    {
        return end($this->storage);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->storage);
    }
}
