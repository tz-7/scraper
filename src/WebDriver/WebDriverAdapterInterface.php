<?php

namespace Tz7\WebScraper\WebDriver;


interface WebDriverAdapterInterface extends WebElementSearchContextInterface
{
    /**
     * @param string $url
     *
     * @return WebElementAdapterInterface
     */
    public function get($url);

    /**
     * @param string $url
     *
     * @return string
     */
    public function download($url);

    /**
     * @param WebElementAdapterInterface      $formAdapter
     * @param WebElementAdapterInterface|null $buttonAdapter
     * @param array|null                      $fields
     *
     * @return WebElementAdapterInterface
     */
    public function submit(
        WebElementAdapterInterface $formAdapter,
        WebElementAdapterInterface $buttonAdapter = null,
        array $fields = null
    );

    /**
     * @return string
     */
    public function getCurrentURL();

    /**
     * @return mixed
     */
    public function getDriver();

    /**
     * @TODO Sure?
     *
     * @return WebElementSelectorFactoryInterface
     */
    public function getSelectorFactory();

    /**
     * @return mixed
     */
    public function getSessionData();
}
