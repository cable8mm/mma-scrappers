<?php

namespace Tests\Unit\Sources\BlackCombat\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseEvents;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers\EventsScraper;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventsScraper::class)]
class EventsScraperTest extends TestCase
{
    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function test_scrape_event()
    {
        $fixture = __DIR__.'/../../../../Fixtures/BlackCombat/blackcombat_events.html';

        $html = file_get_contents($fixture);

        if ($html === false) {
            $this->fail('Fixture load failed');
        }

        $http = $this->createMock(HttpClientInterface::class);

        $http->method('get')->willReturn($html);

        $scraper = new EventsScraper(
            $http,
            new ParseEvents()
        );

        $events = $scraper->scrape('https://www.blackcombat-official.com/event.php?page=10');

        $this->assertNotEmpty($events);

        $this->assertCount(16, $events);

        $this->assertEquals('블랙컴뱃 16: EXODUS', $events[0]->name);
        $this->assertEquals('인천광역시 중구 인스파이어 아레나', $events[0]->location);
        $this->assertEquals('2026년 01월 31일', $events[0]->date->format('Y년 m월 d일'));
        $this->assertEquals('/eventDetail.php?eventSeq=285', $events[0]->url);
    }
}
