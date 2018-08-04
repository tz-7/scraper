<?php

namespace Tz7\WebScraper\Test\Command;


use ArrayObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Request\Context;
use Tz7\WebScraper\Request\ElementStack;
use Tz7\WebScraper\Request\History;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;


class CommandTestAbstract extends TestCase
{
    /**
     * @param array $config
     *
     * @return Command
     */
    protected function createCommandWithConfig(array $config)
    {
        /** @var WebDriverAdapterInterface|PHPUnit_Framework_MockObject_MockObject $driver */
        $driver = $this
            ->getMockBuilder(WebDriverAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expressionContext = new ArrayObject();

        return new Command(
            new Context($driver, '', $expressionContext, $config),
            new History([], new ElementStack())
        );
    }
}
