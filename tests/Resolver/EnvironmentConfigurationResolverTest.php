<?php

namespace Tz7\WebScraper\Test\Resolver;

use PHPUnit\Framework\TestCase;
use Tz7\WebScraper\Resolver\EnvironmentConfigurationResolver;

class EnvironmentConfigurationResolverTest extends TestCase
{
    /** @var EnvironmentConfigurationResolver */
    private $resolver;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->resolver = new EnvironmentConfigurationResolver();
    }

    public function testResolveReturnExpectedValue()
    {
        putenv('SCRAPER__TEST__ENV_RESOLVER=Fred');

        $this->assertEquals('Fred', $this->resolver->resolveValue('%scraper.test.env_resolver%'));
    }

    public function testResolveReturnExpectedConfiguration()
    {
        putenv('SCRAPER__TEST__ENV_RESOLVER=Mars');

        $config = ['deep' => ['test' => '%scraper.test.env_resolver%']];
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
