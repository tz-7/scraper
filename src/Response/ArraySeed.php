<?php

namespace Tz7\WebScraper\Response;

use ArrayObject;

class ArraySeed extends Seed implements SeedCanBePlantedInterface
{
    /**
     * @param ArrayObject|array $data
     */
    public function __construct($data = [])
    {
        parent::__construct($data instanceof ArrayObject ? $data : new ArrayObject($data));
    }

    /**
     * @return ArrayObject
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param callable $callback
     */
    public function plant(callable $callback)
    {
        $this->getData()->exchangeArray(array_map($callback, $this->getData()->getArrayCopy()));
    }
}
