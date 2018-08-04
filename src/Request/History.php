<?php

namespace Tz7\WebScraper\Request;


class History
{
    /** @var array */
    private $navigationHistory;

    /** @var ElementStack */
    private $elementStack;

    /**
     * @param array        $navigationHistory
     * @param ElementStack $elementStack
     */
    public function __construct(array $navigationHistory, ElementStack $elementStack)
    {
        $this->navigationHistory = $navigationHistory;
        $this->elementStack      = $elementStack;
    }

    /**
     * @return array
     */
    public function getNavigationHistory()
    {
        return $this->navigationHistory;
    }

    /**
     * @param string $url
     */
    public function appendNavigationHistory($url)
    {
        $this->navigationHistory[] = $url;
    }

    /**
     * @param string $url
     *
     * @return int
     */
    public function countNavigationHistory($url)
    {
        return array_count_values($this->navigationHistory)[$url];
    }

    /**
     * @return ElementStack
     */
    public function getElementStack()
    {
        return $this->elementStack;
    }

    public function __clone()
    {
        $this->elementStack = clone $this->elementStack;
    }
}
