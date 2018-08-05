<?php

namespace Tz7\WebScraper\Command\Middleware;


use League\Tactician\Middleware;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\KeyValueSeed;
use Tz7\WebScraper\Response\Seed;


class NormalizerMiddleware implements Middleware
{
    /**
     * @param Command  $command
     * @param callable $next
     *
     * @return void
     */
    public function execute($command, callable $next)
    {
        $next($command);

        $command->setSeed($this->getNormalizedSeed($command->getSeed()));
    }

    /**
     * @param Seed|null $seed
     *
     * @return Seed|null
     */
    private function getNormalizedSeed(Seed $seed = null)
    {
        if ($seed instanceof ArraySeed)
        {
            return new ArraySeed($this->normalize($seed));
        }

        return $seed;
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    private function normalize($data)
    {
        switch (true)
        {
            case $data instanceof ArraySeed:
                return $this->normalizerArray($data);

            case $data instanceof Seed:
                return $data->getData();

            default:
                return $data;
        }
    }

    /**
     * @param ArraySeed $seed
     *
     * @return array
     */
    private function normalizerArray(ArraySeed $seed)
    {
        $normalized = [];

        foreach ($seed->getData()->getArrayCopy() as $key => $item)
        {
            if ($item instanceof KeyValueSeed)
            {
                $normalized[(string)$item->getKey()] = $this->normalize($item->getData());
            }
            else
            {
                $normalized[$key] = $this->normalize($item);
            }
        }

        return $normalized;
    }
}
