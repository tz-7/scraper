<?php

namespace Tz7\WebScraper\Resolver;


class EnvironmentConfigurationResolver extends AbstractConfigurationResolver
{
    /**
     * @inheritdoc
     */
    public function resolveValue($value)
    {
        if (preg_match('/^%([^%]+)%$/', $value, $match))
        {
            $name = strtoupper(preg_replace(['~\.~'], ['__'], $match[1]));

            return getenv($name) ?: $value;
        }

        return $value;
    }
}
