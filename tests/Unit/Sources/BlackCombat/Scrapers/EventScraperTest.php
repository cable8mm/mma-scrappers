<?php

namespace Tests\Unit\Sources\BlackCombat\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseEvent;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers\EventScraper;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventScraper::class)]
class EventScraperTest extends TestCase
{
    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function test_scrape_event()
    {
        $fixture = __DIR__.'/../../../../Fixtures/BlackCombat/blackcombat_event_287.html';

        $html = file_get_contents($fixture);

        if ($html === false) {
            $this->fail('Fixture load failed');
        }

        $http = $this->createMock(HttpClientInterface::class);

        $http->method('get')->willReturn($html);

        $scraper = new EventScraper(
            $http,
            new ParseEvent()
        );

        $event = $scraper->scrape('https://www.blackcombat-official.com/eventDetail.php?eventSeq=288');

        $this->assertEquals('블랙컵 8강: 브라질 vs 일본', $event->name);
    }
}
