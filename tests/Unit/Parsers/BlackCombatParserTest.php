<?php

namespace Tests\Unit\Parsers;

use Cable8mm\MmaScrapers\Enums\FightMethod;
use Cable8mm\MmaScrapers\Enums\FightStatus;
use Cable8mm\MmaScrapers\Enums\WeightClass;
use Cable8mm\MmaScrapers\Parsers\BlackCombatParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlackCombatParser::class)]
class BlackCombatParserTest extends TestCase
{
    #[Test]
    public function test_parse_events()
    {
        $dir = __DIR__.'/../../Fixtures/BlackCombat/blackcombat_events.html';

        $html = file_get_contents($dir);

        $parser = new BlackCombatParser();

        $events = $parser->parseEvents($html);

        $this->assertCount(16, $events);

        $this->assertEquals('블랙컴뱃 16: EXODUS', $events[0]->name);
        $this->assertEquals('인천광역시 중구 인스파이어 아레나', $events[0]->location);
        $this->assertEquals('2026년 01월 31일', $events[0]->date->format('Y년 m월 d일'));
        $this->assertEquals('/eventDetail.php?eventSeq=285', $events[0]->url);
    }

    #[Test]
    public function test_parse_event()
    {
        $dir = __DIR__.'/../../Fixtures/BlackCombat/blackcombat_event_287.html';

        $html = file_get_contents($dir);

        $parser = new BlackCombatParser();

        $event = $parser->parseEvent($html);

        $this->assertEquals('블랙컵 8강: 브라질 vs 일본', $event->name);
    }

    #[Test]
    public function test_parse_fights()
    {
        $dir = __DIR__.'/../../Fixtures/BlackCombat/blackcombat_event_287.html';

        $html = file_get_contents($dir);

        $parser = new BlackCombatParser();

        $fights = $parser->parseFights($html);

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

    #[Test]
    public function test_parse_fighter()
    {
        $dir = __DIR__.'/../../Fixtures/BlackCombat/blackcombat_fighter_11958177.html';

        $html = file_get_contents($dir);

        $parser = new BlackCombatParser();

        $fighter = $parser->parseFighter($html);

        $this->assertEquals('김명환', $fighter->name);
        $this->assertEquals('맘모스', $fighter->nickname);
        $this->assertEquals('@official_mammoth', $fighter->instagram);
        $this->assertEquals('Extreme 익스트림 컴뱃', $fighter->teamname);
        $this->assertEquals(183, $fighter->height);
        $this->assertEquals(10, $fighter->win);
        $this->assertEquals(4, $fighter->lose);
        $this->assertEquals(0, $fighter->draw);
        $this->assertEquals(292151, $fighter->sherdogId);
    }
}
