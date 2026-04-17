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
    public function test_merge_with_sherdog_id_priority()
    {
        $a = new FighterDTO(name: 'Chan Sung Jung', sherdogId: 123);
        $b = new FighterDTO(name: '정찬성');

        $agg = new FighterAggregator();

        $result = $agg->merge([$b, $a]);

        $this->assertEquals(123, $result->sherdogId);
        $this->assertEquals('Chan Sung Jung', $result->name);
    }
}
