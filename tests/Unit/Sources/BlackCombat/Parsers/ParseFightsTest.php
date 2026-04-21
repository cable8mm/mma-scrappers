<?php

namespace Tests\Unit\Sources\BlackCombat\Parsers;

use Cable8mm\MmaScrapers\Enums\FightMethod;
use Cable8mm\MmaScrapers\Enums\FightStatus;
use Cable8mm\MmaScrapers\Enums\WeightClass;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFights;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParseFights::class)]
class ParseFightsTest extends TestCase
{
    #[Test]
    public function test_parse_fights()
    {
        $fixture = __DIR__.'/../../../../Fixtures/BlackCombat/blackcombat_event_287.html';

        $html = file_get_contents($fixture);

        $fights = (new ParseFights())($html);

        $this->assertCount(10, $fights);

        $this->assertEquals('Youssef Barakat', $fights[0]->redFighter->name);
        $this->assertEquals('정영제', $fights[0]->blueFighter->name);
        $this->assertEquals(FightStatus::FINISHED, $fights[0]->status);
        $this->assertEquals(WeightClass::LIGHTWEIGHT, $fights[0]->weightClass);
        $this->assertEquals(FightMethod::KO, $fights[0]->method);
        $this->assertNull($fights[0]->round);
        $this->assertNull($fights[0]->time);
        $this->assertEquals($fights[0]->blueFighter, $fights[0]->winner);

        $this->assertEquals('Felipe Gheno', $fights[1]->redFighter->name);
        $this->assertEquals('Mukai Rukiya', $fights[1]->blueFighter->name);
        $this->assertEquals(FightStatus::FINISHED, $fights[1]->status);
        $this->assertEquals(WeightClass::BANTAMWEIGHT, $fights[1]->weightClass);
        $this->assertEquals(FightMethod::DECISION, $fights[1]->method);
        $this->assertNull($fights[1]->round);
        $this->assertNull($fights[1]->time);
        $this->assertEquals($fights[1]->blueFighter, $fights[1]->winner);
    }
}
