<?php

namespace Tz7\WebScraper\Request;


use ArrayObject;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;


class Context
{
    /** @var WebDriverAdapterInterface */
    private $driver;

    /** @var string */
    private $urlContextPattern;

    /** @var ArrayObject */
    private $expressionContext;

    /** @var array */
    private $config;

    /**
     * @param WebDriverAdapterInterface $driver
     * @param string                    $urlContextPattern
     * @param ArrayObject               $expressionContext
     * @param array                     $config
     */
    public function __construct(
        WebDriverAdapterInterface $driver,
        $urlContextPattern,
        ArrayObject $expressionContext,
        array $config
    ) {
        $this->driver            = $driver;
        $this->urlContextPattern = $urlContextPattern;
        $this->expressionContext = $expressionContext;
        $this->config            = $config;
    }

    /**
     * @param array $config
     *
     * @return static
     */
    public function createByConfig(array $config)
    {
        return new static(
            $this->driver,
            $this->urlContextPattern,
            $this->expressionContext,
            $config
        );
    }

    /**
     * @TODO Sure?
     *
     * @return WebDriverAdapterInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return string
     */
    public function getUrlContextPattern()
    {
        return $this->urlContextPattern;
    }

    /**
     * @param string $urlContextPattern
     *
     * @return $this
     */
    public function setUrlContextPattern($urlContextPattern)
    {
        $this->urlContextPattern = $urlContextPattern;

        return $this;
    }

    /**
     * @return ArrayObject
     */
    public function getExpressionContext()
    {
        return $this->expressionContext;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addToExpressionContext($key, $value)
    {
        $this->expressionContext->offsetSet($key, $value);

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeConfig($name)
    {
        unset($this->config[$name]);

        return $this;
    }
}
