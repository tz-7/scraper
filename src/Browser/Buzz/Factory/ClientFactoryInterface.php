<?php

namespace Tz7\WebScraper\Browser\Buzz\Factory;


use Buzz\Client\AbstractClient;


interface ClientFactoryInterface
{
    /**
     * @return AbstractClient
     */
    public function create();
}
