<?php

namespace Tz7\WebScraper\Test\Command\Handler;


use ArrayObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\NullLogger;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Command\Handler\Click;
use Tz7\WebScraper\Request\Context;
use Tz7\WebScraper\Request\ElementStack;
use Tz7\WebScraper\Request\History;
use Tz7\WebScraper\WebDriver\AbstractWebElementSelectorFactory;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;


/**
 * @coversDefaultClass \Tz7\WebScraper\Command\Handler\Click
 */
class ClickTest extends TestCase
{
    /**
     * @covers ::run()
     */
    public function testWillClickOnTopWithoutSelector()
    {
        /** @var WebDriverAdapterInterface|PHPUnit_Framework_MockObject_MockObject $driver */
        $driver = $this
            ->getMockBuilder(WebDriverAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config = [
            'command' => 'click'
        ];

        $expressionContext = new ArrayObject();
        $elementStack      = new ElementStack();

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $element */
        $element = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element
            ->expects($this->once())
            ->method('click');
        $element
            ->expects($this->never())
            ->method('findElement');

        $elementStack->append($element);

        $command = new Command(
            new Context($driver, '', $expressionContext, $config),
            new History([], $elementStack)
        );

        $handler = new Click(new NullLogger());
        $handler->run($command);
    }

    /**
     * @covers ::run()
     */
    public function testWillClickOnSelected()
    {
        /** @var WebElementSelectAdapterInterface|PHPUnit_Framework_MockObject_MockObject $selector */
        $selector = $this
            ->getMockBuilder(WebElementSelectAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var AbstractWebElementSelectorFactory|PHPUnit_Framework_MockObject_MockObject $selectorFactory */
        $selectorFactory = $this
            ->getMockBuilder(AbstractWebElementSelectorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $selectorFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($selector);

        /** @var WebDriverAdapterInterface|PHPUnit_Framework_MockObject_MockObject $driver */
        $driver = $this
            ->getMockBuilder(WebDriverAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $driver
            ->expects($this->once())
            ->method('getSelectorFactory')
            ->willReturn($selectorFactory);

        $config = [
            'command'  => 'click',
            'selector' => 'a#here'
        ];

        $expressionContext = new ArrayObject();
        $elementStack      = new ElementStack();

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $element */
        $element = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element
            ->expects($this->once())
            ->method('click');

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $searchContext */
        $searchContext = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchContext
            ->expects($this->never())
            ->method('click');
        $searchContext
            ->expects($this->once())
            ->method('findElement')
            ->with($selector)
            ->willReturn($element);

        $elementStack->append($searchContext);

        $command = new Command(
            new Context($driver, '', $expressionContext, $config),
            new History([], $elementStack)
        );

        $handler = new Click(new NullLogger());
        $handler->run($command);
    }
}
