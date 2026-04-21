<?php

namespace Tests\Unit\Aggregators;

use PHPUnit\Framework\TestCase;
use Cable8mm\MmaScrapers\Aggregators\FighterAggregator;
use Cable8mm\MmaScrapers\Aggregators\FightAggregator;
use Cable8mm\MmaScrapers\DTO\FightDTO;
use Cable8mm\MmaScrapers\DTO\FighterDTO;
use Cable8mm\MmaScrapers\Enums\Source;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(FightAggregator::class)]
class FightAggregatorTest extends TestCase
{
    #[Test]
    public function test_merge_fight()
    {
        $fighterAgg = new FighterAggregator();
        $agg = new FightAggregator($fighterAgg);

        $a = new FightDTO(
            redFighter: new FighterDTO('김명환', sherdogId: 1),
            blueFighter: new FighterDTO('이상훈', sherdogId: 2),
            source: Source::OFFICIAL
        );

        $b = new FightDTO(
            redFighter: new FighterDTO('Myung Hwan Kim', sherdogId: 1),
            blueFighter: new FighterDTO('Sang Hoon Lee', sherdogId: 2),
            source: Source::SHERDOG,
            round: 2
        );

        $merged = $agg->merge($a, $b);

        $this->assertEquals(2, $merged->round);
    }

    #[Test]
    public function test_reverse_fighter_order()
    {
        $fighterAgg = new FighterAggregator();
        $agg = new FightAggregator($fighterAgg);

        $a = new FightDTO(
            redFighter: new FighterDTO('A', sherdogId: 1),
            blueFighter: new FighterDTO('B', sherdogId: 2),
            source: Source::OFFICIAL
        );

        $b = new FightDTO(
            redFighter: new FighterDTO('B', sherdogId: 2),
            blueFighter: new FighterDTO('A', sherdogId: 1),
            source: Source::SHERDOG
        );

        $this->assertTrue($agg->isSameFight($a, $b));
    }
}
