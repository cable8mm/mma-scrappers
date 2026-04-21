<?php

namespace Tests\Unit\Normalizers;

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

    #[Test]
    public function test_normalize_example(): void
    {
        $texts = [
            'LIGHTWEIGHT BOUT' => WeightClass::LIGHTWEIGHT,
            'BANTAMWEIGHT BOUT' => WeightClass::BANTAMWEIGHT,
            'MIDDLEWEIGHT BOUT' => WeightClass::MIDDLEWEIGHT,
            'HEAVYWEIGHT BOUT' => WeightClass::HEAVYWEIGHT,
            'WELTERWEIGHT BOUT' => WeightClass::WELTERWEIGHT,
            'FEATHERWEIGHT BOUT' => WeightClass::FEATHERWEIGHT,
            'FLYWEIGHT BOUT' => WeightClass::FLYWEIGHT,
            '-100.5KG CATCHWEIGHT BOUT' => WeightClass::CATCHWEIGHT,
        ];

        foreach ($texts as $text => $weightClass) {
            $this->assertEquals($weightClass, WeightClassNormalizer::normalize($text));
        }
    }
}
