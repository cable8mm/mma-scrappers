<?php

namespace Tests\Unit\Sources\Sherdog\Scrapers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\Sherdog\Scrapers\FighterScraper;
use Cable8mm\MmaScrapers\Sources\Sherdog\Parsers\ParseFighter;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FighterScraper::class)]
class FighterScraperTest extends TestCase
{
    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function test_scrapes_sherdog_fighter()
    {
        $fixture = __DIR__.'/../../../../Fixtures/Sherdog/fighter.html';

        $html = file_get_contents($fixture);

        if ($html === false) {
            $this->fail('Fixture load failed');
        }

        $http = $this->createMock(HttpClientInterface::class);

        $http->method('get')->willReturn($html);

        $scraper = new FighterScraper(
            $http,
            new ParseFighter()
        );

        $fighter = $scraper->scrapeById(292151);

        $this->assertEquals('Aaron Jeffery', $fighter->name);
        $this->assertNull($fighter->nickname);
        $this->assertEquals('Niagara Top Team, Aegis MMA, Kill Cliff FC', $fighter->teamname);
        $this->assertEquals(188, $fighter->height);
        $this->assertEquals(16, $fighter->win);
        $this->assertEquals(6, $fighter->lose);
        $this->assertEquals(127631, $fighter->sherdogId);
    }
}
