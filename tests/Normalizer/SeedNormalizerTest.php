<?php

namespace Tz7\WebScraper\Test\Normalizer;


use Tz7\WebScraper\Normalizer\SeedNormalizer;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\KeyValueSeed;
use Tz7\WebScraper\Test\Command\CommandTestAbstract;


/**
 * @coversDefaultClass \Tz7\WebScraper\Normalizer\SeedNormalizer
 */
class SeedNormalizerTest extends CommandTestAbstract
{
    /**
     * @covers ::execute()
     */
    public function testNestedNormalization()
    {
        $normalizer = new SeedNormalizer();

        $seed = new ArraySeed(
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
        );

        $actual = $normalizer->getNormalizedSeed($seed);

        $expected = new ArraySeed([
            'f' => [
                'fk1' => 'fv1',
                'fk2' => 'fv2'
            ],
            's' => [
                'sk1' => 'sv1',
                'sk2' => 'sv2'
            ]
        ]);

        $this->assertEquals($expected, $actual);
    }
}
