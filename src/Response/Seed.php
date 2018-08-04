<?php

namespace Tz7\WebScraper\Response;


abstract class Seed
{
    /** @var mixed */
    protected $data;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    abstract public function getData();
}
