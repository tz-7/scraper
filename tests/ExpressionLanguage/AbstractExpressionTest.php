<?php

namespace Tz7\WebScraper\Test\ExpressionLanguage;


use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\ExpressionLanguage\ExpressionLanguageProvider;


abstract class AbstractExpressionTest extends TestCase
{
    /** @var ExpressionLanguage */
    private $language;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->language = new ExpressionLanguage();
        $this->language->registerProvider(new ExpressionLanguageProvider());
    }

    /**
     * @dataProvider provideTestData
     *
     * @param string $expression
     * @param mixed  $expected
     * @param array  $context
     */
    public function testPregReplace($expression, $expected, array $context = [])
    {
        $result = $this->language->evaluate($expression, $context);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    abstract public function provideTestData();
}
