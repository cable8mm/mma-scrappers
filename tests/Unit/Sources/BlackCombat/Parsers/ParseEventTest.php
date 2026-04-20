<?php

namespace Tests\Unit\Sources\BlackCombat\Parsers;

use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParseEvent::class)]
class ParseEventTest extends TestCase
{
    #[Test]
    public function test_parse_event()
    {
        $fixture = __DIR__.'/../../../../Fixtures/BlackCombat/blackcombat_event_287.html';

        $html = file_get_contents($fixture);

        $event = (new ParseEvent())($html);

        $this->assertEquals('블랙컵 8강: 브라질 vs 일본', $event->name);
    }
}
