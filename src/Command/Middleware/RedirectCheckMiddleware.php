<?php

namespace Tz7\WebScraper\Command\Middleware;


use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Command\Handler\Handler;
use Tz7\WebScraper\Exception\CommandNotFoundException;
use Tz7\WebScraper\Exception\MaximumNestingLevelException;


class RedirectCheckMiddleware implements Middleware
{
    const MAX_REDIRECTION_LOOP = 3;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
            $originalUrlPatternContext = $command->getUrlContextPattern();

            $this->logger->debug(
                'Redirection detected!',
                [
                    'originalUrlPatternContext' => $originalUrlPatternContext,
                    'currentUrl'                => $currentUrl
                ]
            );

            if ($command->countNavigationHistory($currentUrl) >= self::MAX_REDIRECTION_LOOP)
            {
                throw new MaximumNestingLevelException();
            }

            $command->setUrlContextPattern(preg_quote($currentUrl, '/'));

            $onRedirectConfig = $command->getConfigBy(Handler::ON_REDIRECT);
            if ($onRedirectConfig === null)
            {
                $this->logger->debug('No redirect config, continue.');

                return;
            }

            if (empty($onRedirectConfig))
            {
                throw new CommandNotFoundException('No command(s) were defined to handle redirection.');
            }

            $redirectedUrlPatternContext = preg_quote($currentUrl, '/');

            $commandOnRedirect = $command->createChildByConfigName(Handler::ON_REDIRECT);
            $commandOnRedirect->setUrlContextPattern($redirectedUrlPatternContext);

            $this->logger->debug(
                'Handle redirection.',
                [
                    'command' => $commandOnRedirect->getConfigBy(Handler::COMMAND)
                ]
            );

            $next($commandOnRedirect);

            $command->setUrlContextPattern($originalUrlPatternContext);

            if ($command->isDriverMatchingUrlContextPattern())
            {
                $this->logger->debug(
                    'Remote redirected, matching to original url pattern.',
                    [
                        'originalUrlPatternContext' => $originalUrlPatternContext,
                        'driver.currentURL'         => $command->getDriver()->getCurrentURL()
                    ]
                );

                $command->getElementStack()->append($commandOnRedirect->getElementStack()->top());

                break;
            }

            $next($command);

            $currentUrl = $command->getDriver()->getCurrentURL();
        }
    }
}
