<?php

namespace Tests\Unit\Deduplicators;

use PHPUnit\Framework\TestCase;
use Cable8mm\MmaScrapers\Deduplicators\FightDeduplicator;
use Cable8mm\MmaScrapers\DTO\FightDTO;
use Cable8mm\MmaScrapers\DTO\FighterDTO;
use Cable8mm\MmaScrapers\Enums\Source;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(FightDeduplicator::class)]
class FightDeduplicatorTest extends TestCase
{
    #[Test]
    public function test_group_same_fights()
    {
        $fighterA = new FighterDTO('A');
        $fighterB = new FighterDTO('B');

        $date = new \DateTimeImmutable('2024-01-01');

        $fight1 = new FightDTO($fighterA, $fighterB, fightDate: $date, source: Source::SHERDOG);
        $fight2 = new FightDTO($fighterA, $fighterB, fightDate: $date, source: Source::TAPOLOGY);

        $deduplicator = new FightDeduplicator();

        $groups = $deduplicator->group([$fight1, $fight2]);

        $this->assertCount(1, $groups);
        $this->assertCount(2, array_values($groups)[0]);
    }
}
