<?php

namespace Tz7\WebScraper\Command\Middleware;


use League\Tactician\Middleware;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Exception\ElementNotFoundException;


class NullElementMiddleware implements Middleware
{
    /**
     * @param Command  $command
     * @param callable $next
     *
     * @return void
     */
    public function execute($command, callable $next)
    {
        try
        {
            $next($command);
        }
        catch (ElementNotFoundException $exception)
        {
        }
    }
}
