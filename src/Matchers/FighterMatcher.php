<?php

namespace Cable8mm\MmaScrapers\Matchers;

use Cable8mm\MmaScrapers\DTO\FighterDTO;

class FighterMatcher
{
    private array $aliases = [
        '정찬성' => ['chan sung jung', 'jung chan sung', 'korean zombie'],
        '김동현' => ['dong hyun kim', 'stun gun'],
    ];

    public function isSame(FighterDTO $a, FighterDTO $b): bool
    {
        // 1️⃣ Sherdog ID
        if ($this->matchBySherdogId($a, $b)) {
            return true;
        }

        // 2️⃣ Alias dictionary
        if ($this->matchByAlias($a->name, $b->name)) {
            return true;
        }

        // 3️⃣ Fuzzy matching
        if ($this->matchByFuzzy($a->name, $b->name)) {
            return true;
        }

        return false;
    }

    private function matchBySherdogId(FighterDTO $a, FighterDTO $b): bool
    {
        return $a->sherdogId !== null
            && $b->sherdogId !== null
            && $a->sherdogId === $b->sherdogId;
    }

    private function matchByAlias(string $a, string $b): bool
    {
        $a = $this->normalize($a);
        $b = $this->normalize($b);

        foreach ($this->aliases as $key => $list) {
            $all = array_merge([$key], $list);

            if (in_array($a, $all) && in_array($b, $all)) {
                return true;
            }
        }

        return false;
    }

    private function matchByFuzzy(string $a, string $b): bool
    {
        $a = $this->normalize($a);
        $b = $this->normalize($b);

        similar_text($a, $b, $percent);

        return $percent > 85;
    }

    private function normalize(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);

        $parts = explode(' ', $name);
        sort($parts);

        return implode(' ', $parts);
    }
}
