<?php

namespace Tz7\WebScraper\Test\ExpressionLanguage;


class StripTagsExpressionText extends AbstractExpressionTest
{
    /**
     * @inheritdoc
     */
    public function provideTestData()
    {
        return [
            'Remove tags from text' => [
                'expression' => 'strip_tags("This<br>is one.")',
                'expected'   => 'Thisis one.'
            ]
        ];
    }
}
