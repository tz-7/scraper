<?php

namespace Tz7\WebScraper\Test\ExpressionLanguage;


class PregReplaceExpressionTest extends AbstractExpressionTest
{
    /**
     * @return array
     */
    public function provideTestData()
    {
        return [
            'Replace details to download in link'          => [
                'expression' => 'preg_replace("/details/", "download", "some.php?action=details&something=else")',
                'expected'   => 'some.php?action=download&something=else'
            ],
            'Replace & to + and append value from context' => [
                'expression' => 'preg_replace("/&/", "+", "1 & 1") ~ " = " ~ two',
                'expected'   => '1 + 1 = 2',
                'context'    => [
                    'two' => 2
                ]
            ]
        ];
    }
}
