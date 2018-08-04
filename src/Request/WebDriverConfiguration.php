<?php

namespace Tz7\WebScraper\Request;


class WebDriverConfiguration
{
    /** @var string */
    private $userAgent;

    /** @var int */
    private $timeout;

    /** @var string|null */
    private $browser;

    /** @var string|null */
    private $host;

    /** @var int|null */
    private $width;

    /** @var int|null */
    private $height;

    /**
     * @param string      $userAgent
     * @param int         $timeout
     * @param string|null $browser
     * @param string|null $host
     * @param int|null    $width
     * @param int|null    $height
     */
    public function __construct($userAgent, $timeout, $browser = null, $host = null, $width = null, $height = null)
    {
        $this->userAgent = $userAgent;
        $this->timeout   = $timeout;
        $this->browser   = $browser;
        $this->host      = $host;
        $this->width     = $width;
        $this->height    = $height;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return string|null
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @return string|null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }
}
