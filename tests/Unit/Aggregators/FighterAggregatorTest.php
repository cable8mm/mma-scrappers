<?php

namespace Tests\Unit\Aggregators;

use Cable8mm\MmaScrapers\Aggregators\FighterAggregator;
use PHPUnit\Framework\TestCase;
use Cable8mm\MmaScrapers\DTO\FighterDTO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(FighterAggregator::class)]
class FighterAggregatorTest extends TestCase
{
    #[Test]
    public function test_merge_with_sherdog_id()
    {
        $agg = new FighterAggregator();

        $a = new FighterDTO(name: '김명환', sherdogId: 292151);
        $b = new FighterDTO(name: 'Myung Hwan Kim', sherdogId: 292151, win: 10);

        $merged = $agg->merge($a, $b);

        $this->assertEquals(292151, $merged->sherdogId);
        $this->assertEquals(10, $merged->win);
    }

    #[Test]
    public function test_name_matching()
    {
        $agg = new FighterAggregator();

        $a = new FighterDTO(name: '정찬성');
        $b = new FighterDTO(name: 'Jung Chan Sung');

        $this->assertTrue($agg->isSameFighter($a, $b));
    }
}
