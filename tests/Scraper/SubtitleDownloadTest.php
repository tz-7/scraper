<?php

namespace Tz7\WebScraper\Test\Scraper;


use ArrayObject;
use Tz7\WebScraper\Scraper;


class SubtitleDownloadTest extends AbstractScraperTest
{
    /**
     * @dataProvider provideScrapers
     *
     * @param Scraper $scraper
     */
    public function testFeliratokInfo(Scraper $scraper)
    {
        $config = [
            'command'      => 'navigate',
            'url'          => '"http://www.feliratok.info"',
            'processed_by' => [
                'command'      => 'form_submit',
                'form'         => '.search_dev > form',
                'submit'       => 'input#gomb',
                'fields'       => [
                    'search' => 'Stargate SG-1 10x09',
                    'nyelv'  => 'Magyar'
                ],
                'processed_by' => [
                    'command'  => 'element_sequence',
                    'sequence' => 'xpath:://tr[@id="vilagit"]',
                    'on_each'  => [
                        'command' => 'map_element',
                        'key'     => [
                            'command'    => 'evaluate_element',
                            'selector'   => 'div.eredeti',
                            'expression' => 'element.getText()'
                        ],
                        'value'   => [
                            'command'    => 'evaluate_element',
                            'selector'   => 'a[href*="letolt"]',
                            'expression' => 'absoluteUrl(driver, element.getAttribute("href"))'
                        ]
                    ],
                ]
            ]
        ];

        /** @var ArrayObject $data */
        $data = $scraper->scrape($config)->getData();

        $this->assertInstanceOf(ArrayObject::class, $data);
        $this->assertGreaterThan(0, $data->count());

        foreach ($data as $name => $link)
        {
            $this->assertContains('pajzsokat maximumra', $scraper->download($link));
        }
    }

    /**
     * @dataProvider provideScrapers
     *
     * @param Scraper $scraper
     */
    public function testHosszupuska(Scraper $scraper)
    {
        $expressionContext = [
            'baseUrl' => 'http://hosszupuskasub.com',
            'search_query'   => [
                'cim'        => 'The Expanse',
                'evad'       => 's03',
                'resz'       => 'e10',
                'nyelvtipus' => 1,
                'x'          => mt_rand(4, 16),
                'y'          => mt_rand(4, 16)
            ],
        ];

        $config = [
            'command'      => 'navigate',
            'url'          => 'baseUrl',
            'processed_by' => [
                'command'      => 'navigate',
                'url'          => 'baseUrl ~ "/sorozatok.php?" ~ http_build_query(search_query)',
                'processed_by' => [
                    'command'  => 'element_sequence',
                    'sequence' => 'xpath:://tr[td[a[@id="menu"]]]',
                    'on_each'  => [
                        'command' => 'map_element',
                        'key'     => [
                            'command'    => 'evaluate_element',
                            'selector'   => 'xpath:://td[@align="left" and a[@id="menu"]]',
                            'expression' => 'trim(strip_tags(element.getText()))'
                        ],
                        'value'   => [
                            'command'    => 'evaluate_element',
                            'selector'   => 'xpath:://a[contains(@href, "download.php") and @target="_parent"]',
                            'expression' => 'absoluteUrl(driver, element.getAttribute("href"))'
                        ]
                    ],
                ]
            ]
        ];

        /** @var ArrayObject $data */
        $data = $scraper->scrape($config, $expressionContext)->getData();

        $this->assertInstanceOf(ArrayObject::class, $data);
        $this->assertGreaterThan(0, $data->count());

        foreach ($data as $name => $link)
        {
            $this->assertContains('Expanse', $name);
            $this->assertContains('zip', $link);
            $this->assertNotContains('adf.ly', $link);
        }
    }
}
