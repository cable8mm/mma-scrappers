<?php

namespace Tests\E2E;

use PHPUnit\Framework\TestCase;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFights;
use Cable8mm\MmaScrapers\Aggregators\FighterAggregator;
use Cable8mm\MmaScrapers\Aggregators\FightAggregator;
use Cable8mm\MmaScrapers\Services\FightDeduplicator;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class BlackCombatPipelineTest extends TestCase
{
    public function test_full_pipeline()
    {
        $html = file_get_contents(
            __DIR__.'/../Fixtures/BlackCombat/event_detail.html'
        );

        // 1️⃣ Parse
        $parseFights = new ParseFights();
        $fights = $parseFights($html);

        $this->assertNotEmpty($fights);

        // 2️⃣ Aggregator 준비
        $fighterAgg = new FighterAggregator();
        $fightAgg = new FightAggregator($fighterAgg);

        // 3️⃣ Dedup
        $dedup = new FightDeduplicator($fightAgg);
        $result = $dedup->deduplicate($fights);

        // 4️⃣ 최소 검증
        $this->assertNotEmpty($result);
        $this->assertLessThanOrEqual(count($fights), count($result));
    }
}
