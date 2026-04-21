<?php

namespace Tests\Unit\Sources\Sherdog\Parsers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Cable8mm\MmaScrapers\Sources\Sherdog\Parsers\ParseFighter;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ParseFighter::class)]
class ParseFighterTest extends TestCase
{
    #[Test]
    public function test_parses_sherdog_fighter()
    {
        $fixture = __DIR__.'/../../../../Fixtures/Sherdog/fighter.html';

        $html = file_get_contents($fixture);

        if ($html === false) {
            $this->fail('Fixture load failed');
        }

        $parser = new ParseFighter();

        $fighter = $parser($html);

        $this->assertEquals('Aaron Jeffery', $fighter->name);
        $this->assertNull($fighter->nickname);
        $this->assertEquals('Niagara Top Team, Aegis MMA, Kill Cliff FC', $fighter->teamname);
        $this->assertEquals(188, $fighter->height);
        $this->assertEquals(16, $fighter->win);
        $this->assertEquals(6, $fighter->lose);
        $this->assertEquals(127631, $fighter->sherdogId);
    }
}
