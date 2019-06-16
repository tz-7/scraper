<?php

namespace Tz7\WebScraper\Resolver;


use RuntimeException;


abstract class AbstractConfigurationResolver
{
    /**
     * @param array $config
     *
     * @return array
     */
    public function resolve(array $config)
    {
        $this->resolveRecursive($config);

        return $config;
    }
    /**
     * @param string $name
     *
     * @return string
     *
     * @throws RuntimeException
     */
    abstract public function resolveValue($name);

    /**
     * @param array $config
     */
    private function resolveRecursive(array &$config)
    {
        foreach ($config as $key => &$value)
        {
            if (is_array($value))
            {
                $this->resolveRecursive($value);
            }
            elseif (is_scalar($value))
            {
                $value = $this->resolveValue($value);
            }
        }
    }
}
