<?php

namespace Tz7\WebScraper\Test\ExpressionLanguage;


class TrimExpressionTest extends AbstractExpressionTest
{
    /**
     * @inheritdoc
     */
    public function provideTestData()
    {
        return [
            'Trim white spaces'      => [
                'expression' => 'trim("   space    ")',
                'expected'   => 'space'
            ],
            'Trim leading backslash' => [
                'expression' => 'trim("/some.php", "/")',
                'expected'   => 'some.php'
            ]
        ];
    }
}
