<?php

namespace Tz7\WebScraper\ExpressionLanguage;


use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementAdapterInterface;


class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            ExpressionFunction::fromPhp('array_merge'),
            ExpressionFunction::fromPhp('preg_replace'),
            ExpressionFunction::fromPhp('preg_split'),
            ExpressionFunction::fromPhp('strip_tags'),
            ExpressionFunction::fromPhp('trim'),
            ExpressionFunction::fromPhp('mt_rand'),
            ExpressionFunction::fromPhp('urlencode'),
            ExpressionFunction::fromPhp('http_build_query'),
            new ExpressionFunction(
                'preg_match',
                function ($pattern, $subject)
                {
                    return sprintf('preg_match(%s, %s)', $pattern, $subject);
                },
                function (array $values, $pattern, $subject)
                {
                    preg_match($pattern, $subject, $matches);
                    array_shift($matches);

                    return $matches;
                }
            ),
            new ExpressionFunction(
                'alphaNumeric',
                function ($str)
                {
                    return sprintf('(is_string(%1$s) ? alphaNumeric(%1$s) : %1$s)', $str);
                },
                function (array $values, $str)
                {
                    return is_string($str) ? $this->alphaNumeric($str, '') : $str;
                }
            ),
            new ExpressionFunction(
                'slugify',
                function ($str)
                {
                    return sprintf('(is_string(%1$s) ? slugify(%1$s) : %1$s)', $str);
                },
                function (array $values, $str)
                {
                    return is_string($str) ? $this->slugify($str, ' ') : $str;
                }
            ),
            new ExpressionFunction(
                'absoluteUrl',
                function (WebDriverAdapterInterface $driver, $uri)
                {
                    return sprintf('absoluteUrl(driver, %s)', $uri);
                },
                function (array $values, WebDriverAdapterInterface $driver, $uri)
                {
                    return $this->absoluteUrl($driver, $uri);
                }
            ),
            new ExpressionFunction(
                'relativeUrl',
                function ($uri)
                {
                    return sprintf('relativeUrl(%s)', $uri);
                },
                function (array $values, $uri)
                {
                    return $this->relativeUrl($uri);
                }
            ),
            new ExpressionFunction(
                'preg_match',
                function ($pattern, $subject)
                {
                    return sprintf('preg_match(%s, %s)', $pattern, $subject);
                },
                function (array $values, $pattern, $subject)
                {
                    preg_match($pattern, $subject, $matches);

                    return $matches;
                }
            ),
            new ExpressionFunction(
                'preg_match_all',
                function ($pattern, $subject)
                {
                    return sprintf('preg_match(%s, %s)', $pattern, $subject);
                },
                function (array $values, $pattern, $subject)
                {
                    preg_match_all($pattern, $subject, $matches);

                    return $matches[1];
                }
            ),
            new ExpressionFunction(
                'json_decode',
                function ($json)
                {
                    return sprintf('json_decode(%s)', $json);
                },
                function (array $values, $json)
                {
                    return json_decode($json, true);
                }
            ),
            new ExpressionFunction(
                'find_element',
                function (WebDriverAdapterInterface $driver, WebElementAdapterInterface $element, $selector)
                {
                    return sprintf('find_element(driver, element, %s)', $selector);
                },
                function (array $values, WebDriverAdapterInterface $driver, WebElementAdapterInterface $element, $selector)
                {
                    return $element->findElement(
                        $driver->getSelectorFactory()->create($selector)
                    );
                }
            ),
            new ExpressionFunction(
                'find_elements',
                function (WebDriverAdapterInterface $driver, WebElementAdapterInterface $element, $selector)
                {
                    return sprintf('find_element(driver, element, %s)', $selector);
                },
                function (array $values, WebDriverAdapterInterface $driver, WebElementAdapterInterface $element, $selector)
                {
                    return $element->findElements(
                        $driver->getSelectorFactory()->create($selector)
                    );
                }
            ),
        ];
    }

    /**
     * @param string $string
     * @param string $replace
     *
     * @return string
     */
    public function alphaNumeric($string, $replace = '')
    {
        return preg_replace('/[^\w!\s-]/', $replace, $string);
    }

    /**
     * Normalize by Google
     *
     * @param string $string
     *
     * @return string
     */
    public function normalize($string)
    {
        return preg_replace('/[\x80-\xFF]+/', '', \Normalizer::normalize($string, \Normalizer::FORM_D));
    }

    /**
     * Slugify by Google
     *
     * @param string $string
     * @param string $delimeter
     *
     * @return string
     */
    public function slugify($string, $delimeter = "_")
    {
        return trim(strtolower(preg_replace('/[^\w\d]+/', $delimeter, $this->normalize($string))), $delimeter);
    }

    /**
     * @param WebDriverAdapterInterface $driver
     * @param string                    $uri
     *
     * @return string
     */
    public function absoluteUrl(WebDriverAdapterInterface $driver, $uri)
    {
        return (string) UriResolver::resolve(
            new Uri($driver->getCurrentURL()),
            new Uri($uri)
        );
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    public function relativeUrl($uri)
    {
        $uri = new Uri($uri);

        return Uri::composeComponents(
            '',
            '',
            $uri->getPath(),
            $uri->getQuery(),
            $uri->getFragment()
        );
    }
}
