<?php

namespace Tz7\WebScraper\Formatter;


use Monolog\Formatter\LineFormatter;


class VerboseLineFormatter extends LineFormatter
{
    /**
     * @param array $record
     *
     * @return array|mixed|string
     */
    public function format(array $record)
    {
        if (isset($record['context']['screen']))
        {
            unset($record['context']['screen']);
        }

        return parent::format($record);
    }
}
