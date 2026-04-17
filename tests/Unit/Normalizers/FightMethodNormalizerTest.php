<?php

namespace Tests\Unit\Normalizers;

use Cable8mm\MmaScrapers\Enums\FightMethod;
use Cable8mm\MmaScrapers\Normalizers\FightMethodNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FightMethodNormalizer::class)]
class FightMethodNormalizerTest extends TestCase
{
    #[Test]
    public function test_normalize(): void
    {
        $items = FightMethod::cases();

        foreach ($items as $item) {
            $this->assertEquals($item, FightMethodNormalizer::normalize($item->value));
        }

        $this->assertNull(FightMethodNormalizer::normalize('unknown method'));
    }
}
