<?php

namespace Tz7\WebScraper\Test\Scraper;


use League\Tactician\CommandBus;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\Locator\InMemoryLocator;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Middleware\CommandHandlerMiddleware;
use Tz7\WebScraper\Command\Middleware\NormalizerMiddleware;
use Tz7\WebScraper\Command\Middleware\PlantationMiddleware;
use Tz7\WebScraper\Command\Middleware\RedirectCheckMiddleware;
use Tz7\WebScraper\Command\Middleware\ScreenshotMiddleware;
use Tz7\WebScraper\Command\Middleware\TreeWalkMiddleware;
use Tz7\WebScraper\ExpressionLanguage\ExpressionLanguageProvider;
use Tz7\WebScraper\Factory\HandlerCollectionFactory;
use Tz7\WebScraper\Formatter\VerboseLineFormatter;
use Tz7\WebScraper\Normalizer\SeedNormalizer;
use Tz7\WebScraper\Resolver\AbstractConfigurationResolver;
use Tz7\WebScraper\Resolver\EnvironmentConfigurationResolver;
use Tz7\WebScraper\Scraper;
use Tz7\WebScraper\Test\WebDriver\AbstractWebDriverTest;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;


abstract class AbstractScraperTest extends AbstractWebDriverTest
{
    /** @var EnvironmentConfigurationResolver */
    private $configurationResolver;

    /**
     * @return array
     */
    public function provideScrapers()
    {
        return [
            'Using remote web driver' => [
                $this->createScraper(
                    $this->getWebDriverFactory()->createWebDriverByConfiguration(
                        $this->createRemoteWebDriverConfiguration()
                    ),
                    md5(get_called_class())
                ),
            ],
            'Using local web driver'  => [
                $this->createScraper(
                    $this->getWebDriverFactory()->createWebDriverByConfiguration(
                        $this->createLocalWebDriverConfiguration()
                    ),
                    md5(get_called_class())
                )
            ]
        ];
    }

    /**
     * @return EnvironmentConfigurationResolver
     */
    protected function getConfigurationResolver()
    {
        if ($this->configurationResolver === null)
        {
            $this->configurationResolver = $this->createConfigurationResolver();
        }

        return $this->configurationResolver;
    }

    /**
     * @param WebDriverAdapterInterface $driver
     * @param string|null               $method
     *
     * @return Scraper
     */
    protected function createScraper(WebDriverAdapterInterface $driver, $method = null)
    {
        $logger = $this->createLogger($method);

        return new Scraper(
            $this->buildCommandBus($logger),
            $driver,
            $logger
        );
    }

    /**
     * @param string  $sessionKey
     * @param Scraper $scraper
     */
    protected function saveScraper($sessionKey, Scraper $scraper)
    {
        $this->saveWebDriver($sessionKey, $scraper->getWebDriver());
    }

    /**
     * @param string|null $method
     *
     * @return LoggerInterface
     */
    protected function createLogger($method = null)
    {
        return new Logger('scraper', $this->createLoggerHandlers($method));
    }

    /**
     * @param string|null $method
     *
     * @return HandlerInterface[]
     */
    protected function createLoggerHandlers($method = null)
    {
        //$name = dirname(dirname(__FILE__)) . '/logs/run_' . $method . '_' . uniqid() . '.html';

        return [
            (new StreamHandler('php://stdout'))->setFormatter(new VerboseLineFormatter()),
            //(new StreamHandler($name))->setFormatter(new HtmlLineFormatter())
        ];
    }

    /**
     * @return AbstractConfigurationResolver
     */
    protected function createConfigurationResolver()
    {
        return new EnvironmentConfigurationResolver();
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return CommandBus
     */
    private function buildCommandBus(LoggerInterface $logger)
    {
        return new CommandBus(
            [
                new ScreenshotMiddleware($logger),
                new TreeWalkMiddleware(),
                new NormalizerMiddleware(new SeedNormalizer()),
                new PlantationMiddleware(),
                new RedirectCheckMiddleware(),
                new CommandHandlerMiddleware($this->buildHandlerLocator($logger))
            ]
        );
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return HandlerLocator
     */
    private function buildHandlerLocator(LoggerInterface $logger)
    {
        $collection = (
        new HandlerCollectionFactory(
            $logger,
            new ExpressionLanguage(
                null,
                [
                    new ExpressionLanguageProvider()
                ]
            )
        )
        )->getCommands();

        $locator = new InMemoryLocator();

        foreach ($collection as $handler)
        {
            $locator->addHandler($handler, $handler->getName());
        }

        return $locator;
    }
}
