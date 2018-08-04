<?php

namespace Tz7\WebScraper\Command\Middleware;


use League\Tactician\Middleware;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Command\Handler\Handler;


/**
 * @link https://en.wikipedia.org/wiki/Tree_traversal#In-order
 */
class TreeWalkMiddleware implements Middleware
{
    /**
     * @param Command  $command
     * @param callable $next
     *
     * @return void
     */
    public function execute($command, callable $next)
    {
        if ($command->hasConfiguration(Handler::PREPARED_BY))
        {
            $preProcessor = $command->createChildByConfigName(Handler::PREPARED_BY);

            $this->execute($preProcessor, $next);

            $command->setSeed($preProcessor->getSeed());
        }

        $next($command);

        if ($command->hasConfiguration(Handler::PROCESSED_BY))
        {
            $postProcessor = $command->createChildByConfigName(Handler::PROCESSED_BY);

            $postProcessor->setSeed($command->getSeed());

            $this->execute($postProcessor, $next);

            $command->setSeed($postProcessor->getSeed());
        }
    }
}
