<?php

namespace Tz7\WebScraper\Test\Scraper;


use Tz7\WebScraper\Scraper;


/**
 * @coversDefaultClass \Tz7\WebScraper\Scraper
 */
class ScrapingTestingGroundTest extends AbstractScraperTest
{
    /**
     * @covers ::scrape()
     * @dataProvider provideScrapers
     *
     * @param Scraper $scraper
     */
    public function testLoginWithSuccess(Scraper $scraper)
    {
        putenv('SCRAPER__TEST__SCRAPING_GROUND__USERNAME=admin');
        putenv('SCRAPER__TEST__SCRAPING_GROUND__PASSWORD=12345');

        $config = [
            'command'      => 'navigate',
            'url'          => '"http://testing-ground.scraping.pro/login"',
            'url_context'  => '\/login',
            'processed_by' => [
                'command'      => 'form_submit',
                'form'         => 'form[action="login?mode=login"]',
                'submit'       => 'input[type="submit"]',
                'fields'       => [
                    'usr' => '%scraper.test.scraping_ground.username%',
                    'pwd' => '%scraper.test.scraping_ground.password%'
                ],
                'processed_by' => [
                    'command'  => 'read_text',
                    'selector' => '#case_login > h3'
                ]
            ]
        ];

        $resolvedConfig = $this->getConfigurationResolver()->resolve($config);
        $response       = $scraper->scrape($resolvedConfig);

        $this->assertEquals('WELCOME :)', $response->getData());
    }

    /**
     * @covers ::scrape()
     * @dataProvider provideScrapers
     *
     * @param Scraper $scraper
     */
    public function testUserAgent(Scraper $scraper)
    {
        $config = [
            'command'      => 'navigate',
            'url'          => '"http://testing-ground.scraping.pro/whoami"',
            'processed_by' => [
                'command'  => 'read_text',
                'selector' => '#USER_AGENT'
            ]
        ];

        $resolvedConfig = $this->getConfigurationResolver()->resolve($config);
        $response       = $scraper->scrape($resolvedConfig);

        $this->assertEquals(getenv('WEB_DRIVER_USER_AGENT'), $response->getData());
    }

    /**
     * @covers ::scrape()
     * @dataProvider provideScrapers
     *
     * @param Scraper $scraper
     */
    public function testHead(Scraper $scraper)
    {
        $config = [
            'command'      => 'navigate',
            'url'          => '"http://testing-ground.scraping.pro/invalid"',
            'processed_by' => [
                'command'   => 'read_attribute',
                'selector'  => 'head link[href*=main]',
                'attribute' => 'href'
            ]
        ];

        $resolvedConfig = $this->getConfigurationResolver()->resolve($config);
        $response       = $scraper->scrape($resolvedConfig);

        $this->assertContains('/css/main.css', $response->getData());
    }
}
