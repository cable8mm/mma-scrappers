<?php

namespace Tests\Unit\Sources\BlackCombat\Parsers;

use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseEvents;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParseEvents::class)]
class ParseEventsTest extends TestCase
{
    #[Test]
    public function test_parse_event()
    {
        $fixture = __DIR__.'/../../../../Fixtures/BlackCombat/blackcombat_events.html';

        $html = file_get_contents($fixture);

        $events = (new ParseEvents())($html);

        $this->assertCount(16, $events);

        $this->assertEquals('블랙컴뱃 16: EXODUS', $events[0]->name);
        $this->assertEquals('인천광역시 중구 인스파이어 아레나', $events[0]->location);
        $this->assertEquals('2026년 01월 31일', $events[0]->date->format('Y년 m월 d일'));
        $this->assertEquals('/eventDetail.php?eventSeq=285', $events[0]->url);
    }
}
