<?php

namespace Tz7\WebScraper\Response;


class BoolSeed extends Seed
{
    /**
     * @return bool
     */
    public function getData()
    {
        return (bool) $this->data;
    }
}
