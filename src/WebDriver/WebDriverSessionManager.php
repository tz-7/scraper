<?php

namespace Tz7\WebScraper\WebDriver;


use Doctrine\Common\Cache\Cache;
use Facebook\WebDriver\Exception\WebDriverException;
use Tz7\WebScraper\Factory\WebDriverFactory;
use Tz7\WebScraper\Request\WebDriverConfiguration;


class WebDriverSessionManager
{
    const DEFAULT_TTL = 86400;

    /** @var WebDriverFactory */
    private $webDriverFactory;

    /** @var Cache */
    private $cache;

    /**
     * @param WebDriverFactory $webDriverFactory
     * @param Cache            $cache
     */
    public function __construct(WebDriverFactory $webDriverFactory, Cache $cache)
    {
        $this->webDriverFactory = $webDriverFactory;
        $this->cache            = $cache;
    }

    /**
     * @param string                    $sessionKey
     * @param WebDriverAdapterInterface $webDriverAdapter
     * @param int                       $ttl
     */
    public function saveSession($sessionKey, WebDriverAdapterInterface $webDriverAdapter, $ttl = self::DEFAULT_TTL)
    {
        $this->cache->save($this->generateCacheKey($sessionKey), $webDriverAdapter->getSessionData(), $ttl);
    }

    /**
     * @param string                 $sessionKey
     * @param WebDriverConfiguration $webDriverConfiguration
     *
     * @return WebDriverAdapterInterface
     */
    public function restoreSession($sessionKey, WebDriverConfiguration $webDriverConfiguration)
    {
        $sessionData = $this->cache->fetch($this->generateCacheKey($sessionKey));

        return $this->loadDriver($webDriverConfiguration, $sessionData);
    }

    /**
     * @param WebDriverConfiguration $webDriverConfiguration
     * @param string|false           $sessionData
     *
     * @return WebDriverAdapterInterface
     */
    private function loadDriver(WebDriverConfiguration $webDriverConfiguration, $sessionData)
    {
        if ($sessionData !== false)
        {
            try
            {
                $driver = $this->webDriverFactory->createWebDriverByConfiguration(
                    $webDriverConfiguration,
                    $sessionData
                );
                $driver->getCurrentURL();

                return $driver;
            }
            catch (WebDriverException $e)
            {
            }
        }

        return $this->webDriverFactory->createWebDriverByConfiguration($webDriverConfiguration);
    }

    /**
     * @param string $sessionKey
     *
     * @return string
     */
    private function generateCacheKey($sessionKey)
    {
        return sha1(__METHOD__ . $sessionKey);
    }
}
