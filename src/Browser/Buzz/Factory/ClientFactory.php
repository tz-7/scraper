<?php

namespace Tz7\WebScraper\Browser\Buzz\Factory;


use Buzz\Client\AbstractClient;
use Buzz\Client\Curl;


class ClientFactory implements ClientFactoryInterface
{
    /**
     * @return AbstractClient
     */
    public function create()
    {
        return new Curl();
    }
}
