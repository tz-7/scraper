<?php

namespace Tz7\WebScraper\Test\ExpressionLanguage;


use Symfony\Component\Yaml\Yaml;


class PregMatchExpressionTest extends AbstractExpressionTest
{
    /**
     * @return array
     */
    public function provideTestData()
    {
        $yaml = Yaml::parse(file_get_contents(__DIR__ . '/test.yml'));

        return array_merge(
            [
                'Match key in url' => [
                    'expression' => 'preg_match("/some.php\\\?key=(\\\d+)/", "some.php?key=123456")',
                    'expected'   => [123456]
                ]
            ],
            $yaml['preg_match']
        );
    }
}
