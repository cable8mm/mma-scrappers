<?php

namespace Tests\Promotions\Tapology;

use Cable8mm\MmaScrapers\Enums\WeightClass;
use Cable8mm\MmaScrapers\Normalizers\WeightClassNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(WeightClassNormalizer::class)]
class WeightClassNormalizerTest extends TestCase
{
    #[Test]
    public function test_normalize(): void
    {
        $items = WeightClass::cases();

        foreach ($items as $item) {
            $this->assertEquals($item, WeightClassNormalizer::normalize($item->value));
        }

        $this->assertNull(WeightClassNormalizer::normalize('unknown weight class'));
    }
}
