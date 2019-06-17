<?php

namespace Tz7\WebScraper\Test\Command\Handler;


use ArrayObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\NullLogger;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Command\Handler\FormSubmit;
use Tz7\WebScraper\ExpressionLanguage\ExpressionLanguageProvider;
use Tz7\WebScraper\Request\Context;
use Tz7\WebScraper\Request\ElementStack;
use Tz7\WebScraper\Request\History;
use Tz7\WebScraper\WebDriver\AbstractWebElementSelectorFactory;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;


/**
 * @coversDefaultClass \Tz7\WebScraper\Command\Handler\FormSubmit
 */
class FormSubmitTest extends TestCase
{
    /**
     * @covers ::run()
     */
    public function testFormSubmit()
    {
        /** @var WebDriverAdapterInterface|PHPUnit_Framework_MockObject_MockObject $driver */
        $driver = $this
            ->getMockBuilder(WebDriverAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var AbstractWebElementSelectorFactory|PHPUnit_Framework_MockObject_MockObject $selectorFactory */
        $selectorFactory = $this
            ->getMockBuilder(AbstractWebElementSelectorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var WebElementSelectAdapterInterface|PHPUnit_Framework_MockObject_MockObject $selector */
        $selector = $this
            ->getMockBuilder(WebElementSelectAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $element */
        $element = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $form */
        $form = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $submit */
        $submit = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $root */
        $root = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $driver
            ->expects($this->once())
            ->method('getSelectorFactory')
            ->willReturn($selectorFactory);

        $selectorFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturn($selector);

        $element
            ->expects($this->once())
            ->method('findElement')
            ->with($selector)
            ->willReturn($form);

        $form
            ->expects($this->once())
            ->method('findElement')
            ->with($selector)
            ->willReturn($submit);

        $fields = [
            'a' => 'b',
            'c' => 'd',
            'e' => 'fff',
            'g' => false
        ];

        $driver
            ->expects($this->once())
            ->method('submit')
            ->with($form, $submit, $fields)
            ->willReturn($root);

        $config = [
            'command'  => 'form_submit',
            'form'     => 'form',
            'submit'   => 'button',
            'fields'   => [
                'a' => 'b',
            ],
            'evaluate' => [
                'c' => '"d"',
                'e' => 'f',
                'g' => 'h',
                'i' => 'h ?: null'
            ],
            'optional' => [
                'e', 'f', 'g', 'i'
            ]
        ];

        $elementStack      = new ElementStack();
        $expressionContext = new ArrayObject(
            [
                'f' => 'fff',
                'h' => false,
                'i' => null,
                'j' => ''
            ]
        );

        $elementStack->append($element);

        $command = new Command(
            new Context($driver, '', $expressionContext, $config),
            new History([], $elementStack)
        );

        $handler = new FormSubmit(
            new NullLogger(),
            new ExpressionLanguage(
                null,
                [
                    new ExpressionLanguageProvider()
                ]
            )
        );
        $handler->run($command);
    }
}
