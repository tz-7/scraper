<?php

namespace Tz7\WebScraper\Response;


class ScalarSeed extends Seed
{
    /**
     * @return string|int
     */
    public function getData()
    {
        return is_scalar($this->data) ? $this->data : (string) $this->data;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getData();
    }
}
