<?php

namespace Tz7\WebScraper\Command\Middleware;


use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Command\Handler\Handler;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;


class ScreenshotMiddleware implements Middleware
{
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
     * @throws Exception
     */
    public function execute($command, callable $next)
    {
        $driver = $command->getDriver();

        $screenBefore = $command->getConfigBy(Handler::SCREEN_BEFORE);
        if ($screenBefore !== null)
        {
            $this->screenshot($driver, $screenBefore);
        }

        try
        {
            $next($command);
        }
        catch (Exception $exception)
        {
            $this->screenshot($driver, $exception->getMessage(), 'error');

            throw $exception;
        }

        $screenAfter = $command->getConfigBy(Handler::SCREEN_AFTER);
        if ($screenAfter !== null)
        {
            $this->screenshot($driver, $screenAfter);
        }
    }

    /**
     * @param WebDriverAdapterInterface $webDriverAdapter
     * @param string                    $message
     * @param string                    $level
     */
    protected function screenshot(WebDriverAdapterInterface $webDriverAdapter, $message, $level = 'debug')
    {
        $driver = $webDriverAdapter->getDriver();

        if (!$driver instanceof RemoteWebDriver)
        {
            return;
        }

        $screen = $driver->takeScreenshot();

        $this->logger->{$level}(
            'Screen: ' . $message,
            [
                'screen' => base64_encode($screen)
            ]
        );
    }
}
