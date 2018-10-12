<?php

namespace Tz7\WebScraper\Command\Handler;


use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tz7\WebScraper\Command\Command;


abstract class Handler
{
    const COMMAND       = 'command';
    const PREPARED_BY   = 'prepared_by';
    const PROCESSED_BY  = 'processed_by';
    const PROPAGATE     = 'propagate';
    const ON_REDIRECT   = 'on_redirect';
    const URL_CONTEXT   = 'url_context';
    const SCREEN_BEFORE = 'screen_before';
    const SCREEN_AFTER  = 'screen_after';
    const SLEEP_AFTER   = 'sleep_after';

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Command $command
     */
    public function run(Command $command)
    {
        $this->logger->debug('Command: ' . $this->getName());
        $command->setConfig($this->getOptionsResolver()->resolve($command->getConfig()));

        if (!$command->getConfigBy(self::PROPAGATE))
        {
            $this->logger->info('Command runs in separate context.');

            $sub = clone $command;
            $this->execute($sub);

            $command->setSeed($sub->getSeed());
        }
        else
        {
            $this->execute($command);
        }

        $sleepAfter = $command->getConfigBy(self::SLEEP_AFTER);
        if ($sleepAfter > 0)
        {
            usleep($sleepAfter * 1000);
        }

        $urlContext = $command->getConfigBy(self::URL_CONTEXT);
        if ($urlContext !== null)
        {
            $command->setUrlContextPattern($urlContext);
        }
    }

    /**
     * @param Command $command
     *
     * @return mixed
     */
    abstract protected function execute(Command $command);

    /**
     * @return OptionsResolver
     */
    protected function getOptionsResolver()
    {
        return (new OptionsResolver())
            ->setDefaults(
                [
                    self::PREPARED_BY   => null,
                    self::PROCESSED_BY  => null,
                    self::PROPAGATE     => true,
                    self::ON_REDIRECT   => null,
                    self::URL_CONTEXT   => null,
                    self::SCREEN_BEFORE => null,
                    self::SCREEN_AFTER  => null,
                    self::SLEEP_AFTER   => 0
                ]
            )
            ->setRequired(
                [
                    self::COMMAND
                ]
            )
            ->setAllowedTypes(self::PROPAGATE, 'bool')
            ->setAllowedTypes(
                self::ON_REDIRECT,
                [
                    'array',
                    'null'
                ]
            )
            ->setAllowedTypes(
                self::URL_CONTEXT,
                [
                    'string',
                    'null'
                ]
            )
            ->setAllowedTypes(
                self::SCREEN_BEFORE,
                [
                    'string',
                    'null'
                ]
            )
            ->setAllowedTypes(
                self::SCREEN_AFTER,
                [
                    'string',
                    'null'
                ]
            )
            ->setAllowedTypes(self::SLEEP_AFTER, 'int');
    }

    /**
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    protected function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, $context);
    }

    /**
     * @return string
     */
    abstract public function getName();
}
