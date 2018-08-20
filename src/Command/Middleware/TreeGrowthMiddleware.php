<?php

namespace Tz7\WebScraper\Command\Middleware;


use League\Tactician\Middleware;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Normalizer\SeedNormalizer;
use Tz7\WebScraper\Response\CommandSeed;
use Tz7\WebScraper\Response\Seed;
use Tz7\WebScraper\Response\SeedCanBePlantedInterface;
use Tz7\WebScraper\Command\Handler\Handler;


/**
 * Walk order @link https://en.wikipedia.org/wiki/Tree_traversal#In-order
 */
class TreeGrowthMiddleware implements Middleware
{
    /** @var SeedNormalizer */
    private $normalizer;

    public function __construct(SeedNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

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

        $this->processNode($command, $next);

        if ($command->hasConfiguration(Handler::PROCESSED_BY))
        {
            $postProcessor = $command->createChildByConfigName(Handler::PROCESSED_BY);

            $postProcessor->setSeed($command->getSeed());

            $this->execute($postProcessor, $next);

            $command->setSeed($postProcessor->getSeed());
        }
    }

    /**
     * @param Command  $command
     * @param callable $next
     */
    private function processNode(Command $command, callable $next)
    {
        $next($command);

        $seed = $command->getSeed();

        if ($seed === null)
        {
            return;
        }

        $command->setSeed($this->growNode($seed, $next));
    }

    /**
     * @param Seed     $seed
     * @param callable $next
     *
     * @return mixed
     */
    private function growNode(Seed $seed, callable $next)
    {
        if ($seed instanceof SeedCanBePlantedInterface)
        {
            $seed->plant(
                function($data) use ($next)
                {
                    if ($data instanceof Seed)
                    {
                        return $this->growNode($data, $next);
                    }

                    return $data;
                }
            );
        }
        elseif ($seed instanceof CommandSeed)
        {
            $command = $seed->getData();

            $this->execute($command, $next);

            $seed = $command->getSeed();
        }

        return $this->normalizer->getNormalizedSeed($seed);
    }
}
