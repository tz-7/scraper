<?php

namespace Tz7\WebScraper\Browser\Buzz\Client;


use Buzz\Client\AbstractClient;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Doctrine\Common\Cache\Cache;


class CachedClient extends AbstractClient
{
    const DEFAULT_TTL = 3600;

    /** @var AbstractClient */
    private $client;

    /** @var Cache */
    private $cache;

    /** @var int */
    private $ttl;

    /**
     * @param AbstractClient $client
     * @param Cache          $cache
     * @param int            $ttl
     */
    public function __construct(AbstractClient $client, Cache $cache, $ttl = self::DEFAULT_TTL)
    {
        $this->client = $client;
        $this->cache  = $cache;
        $this->ttl    = $ttl;
    }

    /**
     * @inheritDoc
     */
    public function send(RequestInterface $request, MessageInterface $response)
    {
        $cacheKey = $this->getCacheKey($request);
        $cached   = $this->cache->fetch($cacheKey);

        if ($cached !== false)
        {
            $cached = unserialize(base64_decode($cached));
        }

        if ($cached instanceof MessageInterface)
        {
            $response->setHeaders($cached->getHeaders());
            $response->setContent($cached->getContent());

            return;
        }

        $this->client->send($request, $response);
        $this->cache->save($cacheKey, base64_encode(serialize($response)), $this->ttl);
    }

    public function setIgnoreErrors($ignoreErrors)
    {
        return $this->client->setIgnoreErrors($ignoreErrors);
    }

    public function getIgnoreErrors()
    {
        return $this->client->getIgnoreErrors();
    }

    public function setMaxRedirects($maxRedirects)
    {
        $this->client->setMaxRedirects($maxRedirects);
    }

    public function getMaxRedirects()
    {
        return $this->client->getMaxRedirects();
    }

    public function setTimeout($timeout)
    {
        $this->client->setTimeout($timeout);
    }

    public function getTimeout()
    {
        return $this->client->getTimeout();
    }

    public function setVerifyPeer($verifyPeer)
    {
        $this->client->setVerifyPeer($verifyPeer);
    }

    public function getVerifyPeer()
    {
        return $this->client->getVerifyPeer();
    }

    public function getVerifyHost()
    {
        return $this->client->getVerifyHost();
    }

    public function setVerifyHost($verifyHost)
    {
        $this->client->setVerifyHost($verifyHost);
    }

    public function setProxy($proxy)
    {
        $this->client->setProxy($proxy);
    }

    public function getProxy()
    {
        return $this->client->getProxy();
    }

    /**
     * @param RequestInterface $request
     *
     * @return string
     */
    private function getCacheKey(RequestInterface $request)
    {
        return md5($request->getMethod() . $request->getHost() . $request->getResource());
    }
}
