<?php

namespace Cable8mm\MmaScrapers\Normalizers;

use Cable8mm\MmaScrapers\Enums\FightStatus;

/**
 * Class responsible for normalizing fight status strings to FightStatus enum values.
 */
class FightStatusNormalizer
{
    /**
    * Normalize a fight status string to a FightStatus enum value.
    *
    * @param string $value The fight status as a string (e.g., "scheduled", "live", "finished").
    * @return FightStatus The corresponding FightStatus enum value.
    */
    public static function normalize(string $value): FightStatus
    {
        $value = strtolower(trim($value));

        return match (true) {
            $value === 'live' => FightStatus::LIVE,
            $value === 'final',
            $value === 'result',
            $value === 'completed',
            $value === 'finished' => FightStatus::FINISHED,
            default => FightStatus::SCHEDULED,
        };
    }
}
