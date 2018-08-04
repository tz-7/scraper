<?php

namespace Tz7\WebScraper\Response;


class KeyValueSeed extends ScalarSeed implements SeedCanBePlantedInterface
{
    /** @var string */
    protected $key;

    /**
     * @param string $key
     * @param mixed  $data
     */
    public function __construct($key, $data)
    {
        parent::__construct($data);

        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param callable $callback
     */
    public function plant(callable $callback)
    {
        $this->key  = $callback($this->key);
        $this->data = $callback($this->data);
    }
}
