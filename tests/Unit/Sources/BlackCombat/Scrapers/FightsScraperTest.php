<?php

namespace Tests\Unit\Sources\BlackCombat\Scrapers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers\FightsScraper;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFights;

#[CoversClass(FightsScraper::class)]
class FightsScraperTest extends TestCase
{
    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function test_scrapes_fights()
    {
        $fixture = __DIR__.'/../../../../Fixtures/BlackCombat/blackcombat_event_287.html';

        $html = file_get_contents($fixture);

        if ($html === false) {
            $this->fail('Fixture load failed');
        }

        $http = $this->createMock(HttpClientInterface::class);

        $http->method('get')->willReturn($html);

        $scraper = new FightsScraper(
            $http,
            new ParseFights()
        );

        $fights = $scraper->scrape('https://www.blackcombat-official.com/eventDetail.php?eventSeq=287');

        $this->assertNotEmpty($fights);
        $this->assertNotNull($fights[0]->redFighter);
    }
}
