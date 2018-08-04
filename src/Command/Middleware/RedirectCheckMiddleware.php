<?php

namespace Tz7\WebScraper\Command\Middleware;


use League\Tactician\Middleware;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Command\Handler\Handler;
use Tz7\WebScraper\Exception\CommandNotFoundException;
use Tz7\WebScraper\Exception\MaximumNestingLevelException;


class RedirectCheckMiddleware implements Middleware
{
    const MAX_REDIRECTION_LOOP = 3;

    /**
     * @param Command  $command
     * @param callable $next
     *
     * @return void
     *
     * @throws CommandNotFoundException
     * @throws MaximumNestingLevelException
     */
    public function execute($command, callable $next)
    {
        $next($command);

        $currentUrl = $command->getDriver()->getCurrentURL();
        while (!$command->isMatchingUrlContextPattern($currentUrl))
        {
            $urlPatternContext = $command->getUrlContextPattern();

            if ($command->countNavigationHistory($currentUrl) >= self::MAX_REDIRECTION_LOOP)
            {
                throw new MaximumNestingLevelException();
            }

            $command->setUrlContextPattern(preg_quote($currentUrl, '/'));

            $onRedirectConfig = $command->getConfigBy(Handler::ON_REDIRECT);
            if ($onRedirectConfig === null)
            {
                return;
            }

            if (empty($onRedirectConfig))
            {
                throw new CommandNotFoundException('No command(s) were defined to handle redirection.');
            }

            $commandOnRedirect = $command->createChildByConfigName(Handler::ON_REDIRECT);
            $commandOnRedirect->setUrlContextPattern(preg_quote($command->getDriver()->getCurrentURL(), '/'));
            $next($commandOnRedirect);

            $command->setUrlContextPattern($urlPatternContext);
            $next($command);
        }
    }
}
