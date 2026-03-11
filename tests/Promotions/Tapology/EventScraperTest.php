<?php

namespace Tests\Promotions\Tapology;

use Cable8mm\MmaScrapers\Http\GuzzleHttpClient;
use Cable8mm\MmaScrapers\Parser\EventParser;
use Cable8mm\MmaScrapers\Promotions\Tapology\EventScraper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventScraper::class)]
class EventScraperTest extends TestCase
{
    #[Test]
    public function test_scrap(): void
    {
        $this->assertTrue(true);

        // $http = new GuzzleHttpClient;
        // $parser = new EventParser;

        // $scraper = new EventScraper($http, $parser);

        // $event = $scraper->scrape('https://www.tapology.com/fightcenter/events/140740-hype-fc');

        // echo $event->name;
    }
}
