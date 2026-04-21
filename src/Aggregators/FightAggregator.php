<?php

namespace Cable8mm\MmaScrapers\Aggregators;

use Cable8mm\MmaScrapers\DTO\FightDTO;

class FightAggregator
{
    public function __construct(
        private FighterAggregator $fighterAggregator
    ) {
    }

    public function merge(FightDTO $a, FightDTO $b): FightDTO
    {
        if (!$this->isSameFight($a, $b)) {
            throw new \InvalidArgumentException('Different fights');
        }

        $red = $this->fighterAggregator->merge($a->redFighter, $b->redFighter);
        $blue = $this->fighterAggregator->merge($a->blueFighter, $b->blueFighter);

        return new FightDTO(
            redFighter: $red,
            blueFighter: $blue,
            source: $this->pickSource($a, $b),
            status: $this->pick($a->status, $b->status),
            weightClass: $this->pick($a->weightClass, $b->weightClass),
            method: $this->pick($a->method, $b->method),
            round: $this->pick($a->round, $b->round),
            time: $this->pick($a->time, $b->time),
            winner: $this->resolveWinner($a, $b)
        );
    }

    /**
     * 같은 경기인지 판단
     */
    public function isSameFight(FightDTO $a, FightDTO $b): bool
    {
        return $this->isSameFighterPair($a, $b);
    }

    private function isSameFighterPair(FightDTO $a, FightDTO $b): bool
    {
        return (
            $this->fighterAggregator->isSameFighter($a->redFighter, $b->redFighter)
            && $this->fighterAggregator->isSameFighter($a->blueFighter, $b->blueFighter)
        )
        ||
        (
            $this->fighterAggregator->isSameFighter($a->redFighter, $b->blueFighter)
            && $this->fighterAggregator->isSameFighter($a->blueFighter, $b->redFighter)
        );
    }

    /**
     * winner 결정 (핵심)
     */
    private function resolveWinner(FightDTO $a, FightDTO $b)
    {
        return $b->winner ?? $a->winner;
    }

    /**
     * source 선택
     */
    private function pickSource(FightDTO $a, FightDTO $b)
    {
        // Sherdog 우선
        if ($b->source->value === 'sherdog') {
            return $b->source;
        }

        return $a->source;
    }

    /**
     * 값 선택
     */
    private function pick(mixed $a, mixed $b): mixed
    {
        return $b ?? $a;
    }
}
