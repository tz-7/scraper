<?php

namespace Tz7\WebScraper\Test\ExpressionLanguage;


class RandomExpressionTest extends AbstractExpressionTest
{
    /**
     * @inheritdoc
     */
    public function provideTestData()
    {
        return [
            'Generate positive numbers' => [
                'expression' => 'mt_rand(1, 10) > 0',
                'expected'   => true
            ],
            'Generate negative numbers' => [
                'expression' => 'mt_rand(-10, -1) < 0',
                'expected'   => true
            ],
            'Negative test'             => [
                'expression' => 'mt_rand(-10, -1) > 0',
                'expected'   => false
            ]
        ];
    }
}
