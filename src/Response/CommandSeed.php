<?php

namespace Tz7\WebScraper\Response;


use Tz7\WebScraper\Command\Command;


class CommandSeed extends Seed
{
    /**
     * @param Command $data
     */
    public function __construct(Command $data)
    {
        parent::__construct($data);
    }

    /**
     * @return Command
     */
    public function getData()
    {
        return $this->data;
    }
}
