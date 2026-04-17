<?php

namespace Tests\Unit\Normalizers;

use Cable8mm\MmaScrapers\Enums\FightStatus;
use Cable8mm\MmaScrapers\Normalizers\FightStatusNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FightStatusNormalizer::class)]
class FightStatusNormalizerTest extends TestCase
{
    #[Test]
    public function test_normalize(): void
    {
        $items = FightStatus::cases();

        foreach ($items as $item) {
            $this->assertEquals($item, FightStatusNormalizer::normalize($item->value));
        }

        $this->assertEquals(FightStatus::SCHEDULED, FightStatusNormalizer::normalize('unknown status'));
    }
}
