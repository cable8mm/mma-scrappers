<?php

namespace Tests\Unit\Matchers;

use PHPUnit\Framework\TestCase;
use Cable8mm\MmaScrapers\Matchers\FighterMatcher;
use Cable8mm\MmaScrapers\DTO\FighterDTO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(FighterMatcher::class)]
class FighterMatcherTest extends TestCase
{
    #[Test]
    public function test_match_by_sherdog_id()
    {
        $a = new FighterDTO('A', sherdogId: 1);
        $b = new FighterDTO('B', sherdogId: 1);

        $matcher = new FighterMatcher();

        $this->assertTrue($matcher->isSame($a, $b));
    }

    #[Test]
    public function test_match_by_fuzzy()
    {
        $a = new FighterDTO('Chan Sung Jung');
        $b = new FighterDTO('Jung Chan Sung');

        $matcher = new FighterMatcher();

        $this->assertTrue($matcher->isSame($a, $b));
    }
}
