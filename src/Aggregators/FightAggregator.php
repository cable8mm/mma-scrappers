<?php

namespace Cable8mm\MmaScrapers\Aggregators;

use Cable8mm\MmaScrapers\DTO\FightDTO;
use Cable8mm\MmaScrapers\DTO\FighterDTO;

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

        // 🔥 핵심: 순서 정렬
        [$bRed, $bBlue] = $this->alignFighters($a, $b);

        $red = $this->fighterAggregator->merge($a->redFighter, $bRed);
        $blue = $this->fighterAggregator->merge($a->blueFighter, $bBlue);

        return new FightDTO(
            redFighter: $red,
            blueFighter: $blue,
            source: $this->pickSource($a, $b),
            status: $this->pick($a->status, $b->status),
            weightClass: $this->pick($a->weightClass, $b->weightClass),
            method: $this->pick($a->method, $b->method),
            round: $this->pick($a->round, $b->round),
            time: $this->pick($a->time, $b->time),
            winner: $this->resolveWinner($a, $b, $red, $blue)
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
    private function resolveWinner(FightDTO $a, FightDTO $b, FighterDTO $red, FighterDTO $blue)
    {
        // 1️⃣ b 기준 우선
        if ($b->winner) {
            return $this->mapWinner($b->winner, $red, $blue);
        }

        if ($a->winner) {
            return $this->mapWinner($a->winner, $red, $blue);
        }

        return null;
    }

    private function mapWinner(FighterDTO $winner, FighterDTO $red, FighterDTO $blue): ?FighterDTO
    {
        if ($this->fighterAggregator->isSameFighter($winner, $red)) {
            return $red;
        }

        if ($this->fighterAggregator->isSameFighter($winner, $blue)) {
            return $blue;
        }

        return null;
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

    private function alignFighters(FightDTO $a, FightDTO $b): array
    {
        // 같은 방향
        if (
            $this->fighterAggregator->isSameFighter($a->redFighter, $b->redFighter)
            && $this->fighterAggregator->isSameFighter($a->blueFighter, $b->blueFighter)
        ) {
            return [$b->redFighter, $b->blueFighter];
        }

        // 반대 방향 → 뒤집기
        if (
            $this->fighterAggregator->isSameFighter($a->redFighter, $b->blueFighter)
            && $this->fighterAggregator->isSameFighter($a->blueFighter, $b->redFighter)
        ) {
            return [$b->blueFighter, $b->redFighter];
        }

        throw new \InvalidArgumentException('Unable to align fighters');
    }
}
