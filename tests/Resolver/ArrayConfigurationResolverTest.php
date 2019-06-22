<?php

namespace Tz7\WebScraper\Test\Resolver;

use PHPUnit\Framework\TestCase;
use Tz7\WebScraper\Resolver\ArrayConfigurationResolver;
use Tz7\WebScraper\Resolver\EnvironmentConfigurationResolver;

class ArrayConfigurationResolverTest extends TestCase
{
    /** @var ArrayConfigurationResolver */
    private $resolver;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->resolver = new ArrayConfigurationResolver(
            [
                'scraper.test.johnson' => 'Fred',
                'scraper.test.planet'  => 'Mars'
            ]
        );
    }

    public function testResolveReturnExpectedValue()
    {
        $this->assertEquals('Fred', $this->resolver->resolveValue('%scraper.test.johnson%'));
    }

    public function testResolveReturnExpectedConfiguration()
    {
        $config = ['deep' => ['test' => '%scraper.test.planet%']];
        $resolved = $this->resolver->resolve($config);

        $this->assertEquals('Mars', $resolved['deep']['test']);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Undefined configuration key: %no.way%
     */
    public function testResolverThrowsExceptionOnMissing()
    {
        $this->resolver->resolveValue('%no.way%');
    }
}
