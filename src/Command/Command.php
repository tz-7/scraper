<?php

namespace Tz7\WebScraper\Command;


use ArrayObject;
use Tz7\WebScraper\Command\Handler\Handler;
use Tz7\WebScraper\Request\Context;
use Tz7\WebScraper\Request\ElementStack;
use Tz7\WebScraper\Request\History;
use Tz7\WebScraper\Response\Seed;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;


class Command
{
    /** @var Context */
    private $context;

    /** @var History */
    private $history;

    /** @var Seed */
    private $seed;

    /**
     * @param Context $context
     * @param History $history
     */
    public function __construct(Context $context, History $history)
    {
        $this->context = $context;
        $this->history = $history;
    }

    /**
     * @return Seed
     */
    public function getSeed()
    {
        return $this->seed;
    }

    /**
     * @param Seed|null $seed
     *
     * @return $this
     */
    public function setSeed(Seed $seed = null)
    {
        $this->seed = $seed;

        return $this;
    }

    /**
     * @TODO Sure?
     *
     * @return WebDriverAdapterInterface
     */
    public function getDriver()
    {
        return $this->context->getDriver();
    }

    /**
     * @param string $url
     */
    public function appendNavigationHistory($url)
    {
        $this->history->appendNavigationHistory($url);
    }

    /**
     * @param string $url
     *
     * @return int
     */
    public function countNavigationHistory($url)
    {
        return $this->history->countNavigationHistory($url);
    }

    /**
     * @return string
     */
    public function getUrlContextPattern()
    {
        return $this->context->getUrlContextPattern();
    }

    /**
     * @param string $urlContextPattern
     *
     * @return $this
     */
    public function setUrlContextPattern($urlContextPattern)
    {
        $this->context->setUrlContextPattern($urlContextPattern);

        return $this;
    }

    /**
     * @param string $currentUrl
     *
     * @return bool
     */
    public function isMatchingUrlContextPattern($currentUrl)
    {
        return preg_match('/' . $this->getUrlContextPattern() . '/i', $currentUrl) > 0;
    }

    /**
     * @return ArrayObject
     */
    public function getExpressionContext()
    {
        return $this->context->getExpressionContext();
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addToExpressionContext($key, $value)
    {
        $this->context->addToExpressionContext($key, $value);

        return $this;
    }

    /**
     * @return ElementStack
     */
    public function getElementStack()
    {
        return $this->history->getElementStack();
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->context->getConfig();
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig($config)
    {
        $this->context->setConfig($config);

        return $this;
    }

    /**
     * @return string
     */
    public function getCommandName()
    {
        return $this->getConfig()[Handler::COMMAND];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasConfiguration($name)
    {
        return array_key_exists($name, $this->getConfig()) && $this->getConfig()[$name] !== null;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeConfig($name)
    {
        $this->context->removeConfig($name);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function getConfigBy($name)
    {
        if (!$this->hasConfiguration($name))
        {
            return null;
        }

        return $this->getConfig()[$name];
    }

    /**
     * @param array $config
     *
     * @return static
     */
    public function createChildByConfig(array $config)
    {
        $child = new static(
            $this->context->createByConfig($config),
            clone $this->history
        );

        return $child;
    }

    /**
     * @param string $configName
     *
     * @return static
     */
    public function createChildByConfigName($configName)
    {
        $child = new static(
            $this->context->createByConfig($this->getConfigBy($configName)),
            clone $this->history
        );

        return $child;
    }
}
