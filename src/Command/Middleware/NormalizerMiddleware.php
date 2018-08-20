<?php

namespace Tz7\WebScraper\Command\Middleware;


use League\Tactician\Middleware;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Normalizer\SeedNormalizer;


class NormalizerMiddleware implements Middleware
{
    /** @var SeedNormalizer */
    private $normalizer;

    /**
     * @param SeedNormalizer $normalizer
     */
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
        $next($command);

        $command->setSeed($this->normalizer->getNormalizedSeed($command->getSeed()));
    }
}
