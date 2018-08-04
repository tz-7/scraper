<?php

namespace Tz7\WebScraper\WebDriver;


interface WebElementSelectorFactoryInterface
{
    /**
     * @param string $type
     * @param string $value
     *
     * @return WebElementSelectAdapterInterface
     */
    public function createByType($type, $value);
}
