<?php

namespace Tz7\WebScraper\Test\Command\Handler;


use ArrayObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\NullLogger;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Command\Handler\CommandSequence;
use Tz7\WebScraper\Request\Context;
use Tz7\WebScraper\Request\ElementStack;
use Tz7\WebScraper\Request\History;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\CommandSeed;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;


/**
 * @coversDefaultClass \Tz7\WebScraper\Command\Handler\CommandSequence
 */
class CommandSequenceTest extends TestCase
{
    /**
     * @covers ::run()
     */
    public function testSequence()
    {
        $config = [
            'command' => 'command_sequence',
            'sequence' => array_fill(0, 5, ['command' => 'test'])
        ];

        /** @var WebDriverAdapterInterface|PHPUnit_Framework_MockObject_MockObject $driver */
        $driver = $this
            ->getMockBuilder(WebDriverAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new Command(
            new Context($driver, '', new ArrayObject(), $config),
            new History([], new ElementStack())
        );

        $handler = new CommandSequence(new NullLogger());
        $handler->run($command);

        $seed = $command->getSeed();

        $this->assertInstanceOf(ArraySeed::class, $seed);

        $array = $seed->getData()->getArrayCopy();

        $this->assertCount(5, $array);

        foreach ($array as $item)
        {
            $this->assertInstanceOf(CommandSeed::class, $item);
        }
    }
}
