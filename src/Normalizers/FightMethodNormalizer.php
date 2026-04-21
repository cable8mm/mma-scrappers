<?php

namespace Cable8mm\MmaScrapers\Normalizers;

use Cable8mm\MmaScrapers\Enums\FightMethod;

/**
 * Class responsible for normalizing fight method strings to FightMethod enum values.
 */
class FightMethodNormalizer
{
    /**
    * Normalize a fight method string to a FightMethod enum value.
    *
    * @param string $method The fight method as a string (e.g., "KO", "Submission", "Decision").
    * @return FightMethod|null The corresponding FightMethod enum value, or null if the method is unrecognized.
    */
    public static function normalize(string $method): ?FightMethod
    {
        $method = strtolower($method);

        if ($method === 'ko') {
            return FightMethod::KO;
        }

        if ($method === 'tko') {
            return FightMethod::TKO;
        }

        if ($method === 'submission') {
            return FightMethod::SUBMISSION;
        }

        if ($method === 'decision') {
            return FightMethod::DECISION;
        }

        if ($method === 'dq' || $method === 'disqualification') {
            return FightMethod::DQ;
        }

        if ($method === 'nc' || $method === 'no contest') {
            return FightMethod::NC;
        }

        return null;
    }
}
