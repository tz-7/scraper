<?php

namespace Tz7\WebScraper\Resolver;


use RuntimeException;


class EnvironmentConfigurationResolver extends AbstractConfigurationResolver
{
    /**
     * @inheritdoc
     */
    public function resolveValue($value)
    {
        if (!preg_match('/^%([^%]+)%$/', $value, $match))
        {
            return $value;
        }

        $name     = strtoupper(preg_replace(['~\.~'], ['__'], $match[1]));
        $resolved = getenv($name);

        if ($resolved !== false)
        {
            return $resolved;
        }

        throw new RuntimeException('Undefined configuration key: ' . $value);
    }
}
