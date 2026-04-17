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

    /**
     * @param FightDTO[] $fights
     */
    public function merge(array $fights): FightDTO
    {
        if (empty($fights)) {
            throw new \InvalidArgumentException('Fights array cannot be empty');
        }

        // 🔥 source priority 정렬
        usort($fights, fn ($a, $b) => $b->source->value <=> $a->source->value);

        return new FightDTO(
            redFighter: $this->mergeFighters($fights, 'redFighter'),
            blueFighter: $this->mergeFighters($fights, 'blueFighter'),
            status: $this->pickEnum($fights, 'status'),
            weightClass: $this->pickEnum($fights, 'weightClass'),
            method: $this->pickEnum($fights, 'method'),
            round: $this->pickInt($fights, 'round'),
            time: $this->pickString($fights, 'time'),
            winner: $this->pickWinner($fights),
            source: $fights[0]->source
        );
    }

    /**
     * FighterDTO merge (핵심)
     */
    private function mergeFighters(array $fights, string $field): FighterDTO
    {
        $fighters = array_map(fn ($fight) => $fight->$field, $fights);

        return $this->fighterAggregator->merge($fighters);
    }

    /**
     * winner 결정 로직 (🔥 중요)
     */
    private function pickWinner(array $fights): ?FighterDTO
    {
        // 1️⃣ source priority 순으로 확인
        foreach ($fights as $fight) {
            if ($fight->winner !== null) {
                return $fight->winner;
            }
        }

        return null;
    }

    private function pickString(array $fights, string $field): ?string
    {
        foreach ($fights as $fight) {
            if (!empty($fight->$field)) {
                return $fight->$field;
            }
        }

        return null;
    }

    private function pickInt(array $fights, string $field): ?int
    {
        foreach ($fights as $fight) {
            if ($fight->$field !== null) {
                return $fight->$field;
            }
        }

        return null;
    }

    private function pickEnum(array $fights, string $field)
    {
        foreach ($fights as $fight) {
            if ($fight->$field !== null) {
                return $fight->$field;
            }
        }

        return null;
    }
}
