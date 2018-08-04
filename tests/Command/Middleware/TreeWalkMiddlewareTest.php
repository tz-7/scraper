<?php

namespace Tz7\WebScraper\Test\Command\Middleware;


use InvalidArgumentException;
use League\Tactician\CommandBus;
use League\Tactician\Middleware;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Command\Middleware\TreeWalkMiddleware;
use Tz7\WebScraper\Response\ScalarSeed;
use Tz7\WebScraper\Test\Command\CommandTestAbstract;


/**
 * @coversDefaultClass \Tz7\WebScraper\Command\Middleware\TreeWalkMiddleware
 */
class TreeWalkMiddlewareTest extends CommandTestAbstract
{
    /**
     * @covers ::execute()
     */
    public function testTreeWalk()
    {
        $this->middlewareCallback(
            function (Command $command)
            {
                switch ($command->getCommandName())
                {
                    case 'pre-processor':
                        return $command->setSeed(new ScalarSeed('pre-processed'));

                    case 'run':
                        $this->assertEquals('pre-processed', $command->getSeed()->getData());

                        return $command->setSeed(new ScalarSeed('result'));

                    case 'post-processor':
                        $this->assertEquals('result', $command->getSeed()->getData());

                        return $command->setSeed(new ScalarSeed('processed-result'));
                }

                throw new InvalidArgumentException('Invalid command.');
            }
        );
    }

    /**
     * @param callable $callback
     */
    protected function middlewareCallback(callable $callback)
    {
        $config = [
            'prepared_by'  => [
                'command' => 'pre-processor'
            ],
            'command'      => 'run',
            'processed_by' => [
                'command' => 'post-processor'
            ]
        ];

        $command = $this->createCommandWithConfig($config);
        $handler = $this->getMockBuilder(Middleware::class)->setMethods(['execute'])->getMock();

        $handler
            ->expects($this->exactly(3))
            ->method('execute')
            ->willReturnCallback($callback);

        $treeWalk   = new TreeWalkMiddleware();
        $commandBus = new CommandBus(
            [
                $treeWalk,
                $handler
            ]
        );

        $commandBus->handle($command);
        $this->assertEquals('processed-result', $command->getSeed()->getData());
    }
}
