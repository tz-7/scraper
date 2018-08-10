<?php

namespace Tz7\WebScraper\Browser\Buzz\Factory;


use Buzz\Client\AbstractClient;
use Doctrine\Common\Cache\Cache;
use Tz7\WebScraper\Browser\Buzz\Client\CachedClient;


class CachedClientFactory implements ClientFactoryInterface
{
    /** @var ClientFactory */
    private $clientFactory;

    /** @var Cache */
    private $cache;

    /**
     * @param ClientFactory $clientFactory
     * @param Cache         $cache
     */
    public function __construct(ClientFactory $clientFactory, Cache $cache)
    {
        $this->clientFactory = $clientFactory;
        $this->cache         = $cache;
    }

    /**
     * @return AbstractClient
     */
    public function create()
    {
        return new CachedClient(
            $this->clientFactory->create(),
            $this->cache
        );
    }
}
