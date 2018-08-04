<?php

namespace Tz7\WebScraper\Response;


interface SeedCanBePlantedInterface
{
    /**
     * @param callable $callback
     */
    public function plant(callable $callback);
}
