<?php

namespace Tz7\WebScraper\Formatter;


use Monolog\Formatter\NormalizerFormatter;
use Monolog\Logger;
use Exception;


class HtmlLineFormatter extends NormalizerFormatter
{
    const SIMPLE_FORMAT = "<div style=\"color: %color%;\">[%datetime%] %channel%.%level_name%: %message% %context% %extra%</div>\n";

    /**
     * Translates Monolog log levels to html color priorities.
     */
    private $logLevels = [
        Logger::DEBUG     => '#bbbbbb',
        Logger::INFO      => '#408040',
        Logger::NOTICE    => '#3080a0',
        Logger::WARNING   => '#c09050',
        Logger::ERROR     => '#f0a040',
        Logger::CRITICAL  => '#F07000',
        Logger::ALERT     => '#C02010',
        Logger::EMERGENCY => '#000000',
    ];

    protected $format;
    protected $allowInlineLineBreaks;
    protected $ignoreEmptyContextAndExtra;
    protected $includeStacktraces;

    /**
     * @param string $format                The format of the message
     * @param string $dateFormat            The format of the timestamp: one supported by DateTime::format
     * @param bool   $allowInlineLineBreaks Whether to allow inline line breaks in log entries
     * @param bool   $ignoreEmptyContextAndExtra
     */
    public function __construct(
        $format = null,
        $dateFormat = null,
        $allowInlineLineBreaks = false,
        $ignoreEmptyContextAndExtra = false
    ) {
        $this->format                     = $format ?: static::SIMPLE_FORMAT;
        $this->allowInlineLineBreaks      = $allowInlineLineBreaks;
        $this->ignoreEmptyContextAndExtra = $ignoreEmptyContextAndExtra;
        parent::__construct($dateFormat);
    }

    public function includeStacktraces($include = true)
    {
        $this->includeStacktraces = $include;
        if ($this->includeStacktraces)
        {
            $this->allowInlineLineBreaks = true;
        }
    }

    public function allowInlineLineBreaks($allow = true)
    {
        $this->allowInlineLineBreaks = $allow;
    }

    public function ignoreEmptyContextAndExtra($ignore = true)
    {
        $this->ignoreEmptyContextAndExtra = $ignore;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $vars          = parent::format($record);
        $vars['color'] = $this->logLevels[$record['level']];

        $output = $this->format;

        if (isset($vars['context']['screen']))
        {
            $output = str_replace(
                '%context%',
                sprintf(
                    '<img src="data:image/png;base64,%s" style="display: block;"/>',
                    $vars['context']['screen']
                ),
                $output
            );
            unset($vars['context']['screen']);
        }

        foreach ($vars['extra'] as $var => $val)
        {
            if (false !== strpos($output, '%extra.' . $var . '%'))
            {
                $output = str_replace('%extra.' . $var . '%', $this->stringify($val), $output);
                unset($vars['extra'][$var]);
            }
        }

        if ($this->ignoreEmptyContextAndExtra)
        {
            if (empty($vars['context']))
            {
                unset($vars['context']);
                $output = str_replace('%context%', '', $output);
            }

            if (empty($vars['extra']))
            {
                unset($vars['extra']);
                $output = str_replace('%extra%', '', $output);
            }
        }

        foreach ($vars as $var => $val)
        {
            if (false !== strpos($output, '%' . $var . '%'))
            {
                $output = str_replace('%' . $var . '%', $this->stringify($val), $output);
            }
        }

        return $output;
    }

    public function formatBatch(array $records)
    {
        $message = '';
        foreach ($records as $record)
        {
            $message .= $this->format($record);
        }

        return $message;
    }

    public function stringify($value)
    {
        return $this->replaceNewlines($this->convertToString($value));
    }

    /**
     * @param Exception $e
     *
     * @return string
     */
    protected function normalizeException($e)
    {
        $previousText = '';
        if ($previous = $e->getPrevious())
        {
            do
            {
                $previousText .= ', ' . get_class($previous) . '(code: ' . $previous->getCode(
                    ) . '): ' . $previous->getMessage() . ' at ' . $previous->getFile() . ':' . $previous->getLine();
            } while ($previous = $previous->getPrevious());
        }

        $str = '[object] ('
               . get_class($e)
               . '(code: '
               . $e->getCode()
               . '): '
               . $e->getMessage()
               . ' at '
               . $e->getFile()
               . ':'
               . $e->getLine()
               . $previousText
               . ')';
        if ($this->includeStacktraces)
        {
            $str .= "\n[stacktrace]\n" . $e->getTraceAsString();
        }

        return $str;
    }

    protected function convertToString($data)
    {
        if (null === $data || is_bool($data))
        {
            return var_export($data, true);
        }

        if (is_scalar($data))
        {
            return (string) $data;
        }

        if (version_compare(PHP_VERSION, '5.4.0', '>='))
        {
            return $this->toJson($data, true);
        }

        return str_replace('\\/', '/', @json_encode($data));
    }

    protected function replaceNewlines($str)
    {
        if ($this->allowInlineLineBreaks)
        {
            return $str;
        }

        return strtr(
            $str,
            [
                "\r\n" => ' ',
                "\r"   => ' ',
                "\n"   => ' '
            ]
        );
    }
}
