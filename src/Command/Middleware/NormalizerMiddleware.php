<?php

namespace Tz7\WebScraper\Command\Middleware;


use League\Tactician\Middleware;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\KeyValueSeed;


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

        $command->setSeed($this->normalize($command->getSeed()));
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    private function normalize($data)
    {
        if ($data instanceof ArraySeed)
        {
            return $this->normalizerArray($data);
        }

        return $data;
    }

    /**
     * @param ArraySeed $seed
     *
     * @return ArraySeed
     */
    private function normalizerArray(ArraySeed $seed)
    {
        $normalized = [];

        foreach ($seed->getData()->getArrayCopy() as $item)
        {
            if ($item instanceof KeyValueSeed)
            {
                $normalized[(string)$item->getKey()] = $this->normalize($item->getData());
            }
            else
            {
                $normalized[] = $item;
            }
        }

        $seed->getData()->exchangeArray($normalized);

        return $seed;
    }
}
