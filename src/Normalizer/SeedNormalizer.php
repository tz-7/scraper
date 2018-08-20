<?php

namespace Tz7\WebScraper\Normalizer;


use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\CommandSeed;
use Tz7\WebScraper\Response\KeyValueSeed;
use Tz7\WebScraper\Response\Seed;


class SeedNormalizer
{
    /**
     * @param Seed|null $seed
     *
     * @return Seed|null
     */
    public function getNormalizedSeed(Seed $seed = null)
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

            case $data instanceof Seed && !($data instanceof CommandSeed):
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
                $normalized[(string) $item->getKey()] = $this->normalize($item->getData());
            }
            else
            {
                $normalized[$key] = $this->normalize($item);
            }
        }

        return $normalized;
    }
}
