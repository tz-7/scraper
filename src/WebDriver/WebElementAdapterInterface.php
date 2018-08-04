<?php

namespace Tz7\WebScraper\WebDriver;


interface WebElementAdapterInterface extends WebElementSearchContextInterface
{
    /**
     * @return $this
     */
    public function click();

    /**
     * @return string
     */
    public function getTagName();

    /**
     * @param string $attributeName
     *
     * @return string|null
     */
    public function getAttribute($attributeName);

    /**
     * @return string
     */
    public function getText();

    /**
     * @return mixed
     */
    public function getElement();
}
