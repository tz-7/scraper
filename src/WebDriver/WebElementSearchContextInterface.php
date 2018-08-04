<?php

namespace Tz7\WebScraper\WebDriver;


interface WebElementSearchContextInterface
{
    /**
     * @param WebElementSelectAdapterInterface $selector
     *
     * @return WebElementAdapterInterface|null
     */
    public function findElement(WebElementSelectAdapterInterface $selector);

    /**
     * @param WebElementSelectAdapterInterface $selector
     *
     * @return WebElementAdapterInterface[]
     */
    public function findElements(WebElementSelectAdapterInterface $selector);
}
