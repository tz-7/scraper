<?php

namespace Tz7\WebScraper\Test\Command\Middleware;


use ArrayObject;
use Tz7\WebScraper\Command\Middleware\NormalizerMiddleware;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\KeyValueSeed;
use Tz7\WebScraper\Test\Command\CommandTestAbstract;


/**
 * @coversDefaultClass \Tz7\WebScraper\Command\Middleware\NormalizerMiddleware
 */
class NormalizerMiddlewareTest extends CommandTestAbstract
{
    /**
     * @covers ::execute()
     */
    public function testNestedNormalization()
    {
        $command    = $this->createCommandWithConfig([]);
        $middleware = new NormalizerMiddleware();

        $command->setSeed(
            new ArraySeed(
                [
                    'f' => new ArraySeed(
                        [
                            new KeyValueSeed('fk1', 'fv1'),
                            new KeyValueSeed('fk2', 'fv2')
                        ]
                    ),
                    's' => new ArraySeed(
                        [
                            new KeyValueSeed('sk1', 'sv1'),
                            new KeyValueSeed('sk2', 'sv2')
                        ]
                    )
                ]
            )
        );

        $middleware->execute($command, function () {});

        $expectedData = new ArrayObject([
            'f' => [
                'fk1' => 'fv1',
                'fk2' => 'fv2'
            ],
            's' => [
                'sk1' => 'sv1',
                'sk2' => 'sv2'
            ]
        ]);

        /** @var ArrayObject $actualData */
        $actualData = $command->getSeed()->getData();

        $this->assertEquals($expectedData, $actualData);
    }
}
