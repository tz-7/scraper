<?php

namespace Tz7\WebScraper\Resolver;


use RuntimeException;


class ArrayConfigurationResolver extends AbstractConfigurationResolver
{
    /** @var array */
    private $source;

    public function __construct(array $source)
    {
        $this->source = $source;
    }

    /**
     * @inheritDoc
     */
    public function resolveValue($value)
    {
        if (!preg_match('/^%([^%]+)%$/', $value, $match))
        {
            return $value;
        }

        $name = $match[1];

        if (!array_key_exists($name, $this->source))
        {
            throw new RuntimeException('Undefined configuration key: ' . $value);
        }

        return $this->source[$name];
    }
}
