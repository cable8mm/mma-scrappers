<?php

namespace Cable8mm\MmaScrapers\Deduplicators;

use Cable8mm\MmaScrapers\DTO\FightDTO;

class FightDeduplicator
{
    /**
     * @param FightDTO[] $fights
     * @return array<string, FightDTO[]>
     */
    public function group(array $fights): array
    {
        $groups = [];

        foreach ($fights as $fight) {
            $key = $this->fingerprint($fight);

            $groups[$key][] = $fight;
        }

        return $groups;
    }

    private function fingerprint(FightDTO $fight): string
    {
        $fighters = [
            $this->normalizeName($fight->redFighter->name),
            $this->normalizeName($fight->blueFighter->name),
        ];

        sort($fighters);

        $date = $fight->fightDate?->format('Y-m-d') ?? 'unknown';

        return md5(implode('-', $fighters) . '-' . $date);
    }

    private function normalizeName(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);

        $parts = explode(' ', $name);
        sort($parts);

        return implode(' ', $parts);
    }
}
