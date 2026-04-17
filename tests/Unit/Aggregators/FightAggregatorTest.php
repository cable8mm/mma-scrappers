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
    public function test_merge_fights()
    {
        $fighterA = new FighterDTO('A');
        $fighterB = new FighterDTO('B');

        $fight1 = new FightDTO($fighterA, $fighterB, round: 1, source: Source::SHERDOG);
        $fight2 = new FightDTO($fighterA, $fighterB, round: 3, source: Source::OFFICIAL);

        $agg = new FightAggregator(new FighterAggregator());

        $result = $agg->merge([$fight2, $fight1]);

        $this->assertEquals(1, $result->round); // Sherdog 우선
    }
}
