<?php

namespace Tests\Unit\Sources\BlackCombat\Parsers;

use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFighter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParseFighter::class)]
class ParseFighterTest extends TestCase
{
    #[Test]
    public function test_parse_fighter()
    {
        $fixture = __DIR__.'/../../../../Fixtures/BlackCombat/blackcombat_fighter_11958177.html';

        $html = file_get_contents($fixture);

        $fighter = (new ParseFighter())($html);

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
