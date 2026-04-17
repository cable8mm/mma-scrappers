<?php

namespace Tests\Unit\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Parsers\BlackCombatParser;
use Cable8mm\MmaScrapers\Scrapers\BlackCombatEventsScraper;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlackCombatEventsScraper::class)]
class BlackCombatEventsScraperTest extends TestCase
{
    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function test_parse_events()
    {
        $dir = __DIR__.'/../../Fixtures/BlackCombat/blackcombat_events.html';

        $html = file_get_contents($dir);

        $http = $this->createMock(HttpClientInterface::class);

        $http->method('get')->willReturn($html);

        $scraper = new BlackCombatEventsScraper(
            $http,
            new BlackCombatParser()
        );

        $events = $scraper->scrape();

        $this->assertCount(16, $events);

        $this->assertEquals('블랙컴뱃 16: EXODUS', $events[0]->name);
        $this->assertEquals('인천광역시 중구 인스파이어 아레나', $events[0]->location);
        $this->assertEquals('2026년 01월 31일', $events[0]->date->format('Y년 m월 d일'));
        $this->assertEquals('/eventDetail.php?eventSeq=285', $events[0]->url);
    }
}
