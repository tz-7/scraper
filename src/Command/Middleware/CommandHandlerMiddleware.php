<?php

namespace Tz7\WebScraper\Command\Middleware;


use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Middleware;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Command\Handler\Handler;


class CommandHandlerMiddleware implements Middleware
{
    /** @var HandlerLocator */
    private $handlerLocator;

    /**
     * @param HandlerLocator $handlerLocator
     */
    public function __construct(HandlerLocator $handlerLocator)
    {
        $this->handlerLocator = $handlerLocator;
    }

    /**
     * @param Command  $command
     * @param callable $next
     *
     * @return void
     */
    public function execute($command, callable $next)
    {
        $this->getHandlerForCommand($command)->run($command);
    }

    /**
     * @param Command $command
     *
     * @return Handler
     */
    private function getHandlerForCommand(Command $command)
    {
        /** @var Handler $handler */
        $handler = $this->handlerLocator->getHandlerForCommand($command->getCommandName());

        return $handler;
    }
}
