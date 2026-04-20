<?php

namespace Tests\Unit\Sources\BlackCombat\Scrapers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers\FighterScraper;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFighter;

#[CoversClass(FighterScraper::class)]
class FighterScraperTest extends TestCase
{
    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function test_scrapes_fighter()
    {
        $fixture = __DIR__.'/../../../../Fixtures/BlackCombat/blackcombat_fighter_11958177.html';

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

        $fighter = $scraper->scrape('https://www.blackcombat-official.com/fighter/84540347');

        $this->assertEquals('김명환', $fighter->name);
        $this->assertNotNull($fighter->sherdogId);
    }
}
