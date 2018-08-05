<?php

namespace Tz7\WebScraper\Response;


class KeyValueSeed extends Seed implements SeedCanBePlantedInterface
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
     * @return mixed
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
        $this->key  = $callback($this->key);
        $this->data = $callback($this->data);
    }
}
