<?php

namespace Tests\Unit\Aggregators;

use Cable8mm\MmaScrapers\Aggregators\EventAggregator;
use Cable8mm\MmaScrapers\DTO\EventDTO;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(EventAggregator::class)]
class EventAggregatorTest extends TestCase
{
    #[Test]
    public function test_same_event()
    {
        $agg = new EventAggregator();

        $a = new EventDTO(
            name: 'UFC 300',
            date: new \DateTimeImmutable('2024-04-13')
        );

        $b = new EventDTO(
            name: 'UFC 300: Pereira vs Hill',
            date: new \DateTimeImmutable('2024-04-13')
        );

        $this->assertTrue($agg->isSameEvent($a, $b));
    }
}
