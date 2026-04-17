<?php

namespace Cable8mm\MmaScrapers\Aggregators;

use Cable8mm\MmaScrapers\DTO\FighterDTO;

class FighterAggregator
{
    /**
     * @param FighterDTO[] $fighters
     */
    public function merge(array $fighters): FighterDTO
    {
        // 1️⃣ Sherdog ID 기준으로 대표 fighter 선택
        $primary = $this->pickPrimary($fighters);

        return new FighterDTO(
            name: $this->pickName($fighters, $primary),
            nickname: $this->pickString($fighters, 'nickname'),
            instagram: $this->pickString($fighters, 'instagram'),
            teamname: $this->pickString($fighters, 'teamname'),
            height: $this->pickString($fighters, 'height'),
            win: $this->pickInt($fighters, 'win'),
            lose: $this->pickInt($fighters, 'lose'),
            draw: $this->pickInt($fighters, 'draw'),
            sherdogId: $this->pickSherdogId($fighters)
        );
    }

    private function pickPrimary(array $fighters): FighterDTO
    {
        foreach ($fighters as $fighter) {
            if ($fighter->sherdogId !== null) {
                return $fighter;
            }
        }

        return $fighters[0];
    }

    private function pickSherdogId(array $fighters): ?int
    {
        foreach ($fighters as $fighter) {
            if ($fighter->sherdogId !== null) {
                return $fighter->sherdogId;
            }
        }

        return null;
    }

    private function pickName(array $fighters, FighterDTO $primary): string
    {
        // 1️⃣ Sherdog 기반 이름 우선
        if ($primary->sherdogId !== null) {
            return $primary->name;
        }

        // 2️⃣ fallback: 가장 긴 이름 (보통 full name)
        usort($fighters, fn ($a, $b) => strlen($b->name) <=> strlen($a->name));

        return $fighters[0]->name;
    }

    private function pickString(array $fighters, string $field): ?string
    {
        foreach ($fighters as $fighter) {
            if (!empty($fighter->$field)) {
                return $fighter->$field;
            }
        }

        return null;
    }

    private function pickInt(array $fighters, string $field): ?int
    {
        foreach ($fighters as $fighter) {
            if ($fighter->$field !== null) {
                return $fighter->$field;
            }
        }

        return null;
    }
}
