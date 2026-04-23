<?php

namespace Cable8mm\MmaScrapers\Matchers;

use Cable8mm\MmaScrapers\DTO\FighterDTO;

/**
 * The FighterMatcher is responsible for determining if two FighterDTOs represent the same fighter.
 *
 * It uses a combination of Sherdog ID matching, alias dictionary, and fuzzy name matching to make this determination.
 *
 * Example usage:
 * $matcher = new FighterMatcher();
 * $fighter1 = new FighterDTO(name: 'Fighter A', sherdogId: 123);
 * $fighter2 = new FighterDTO(name: 'Fighter A', sherdogId: 123);
 * if ($matcher->isSame($fighter1, $fighter2)) {
 *     // The fighters are considered the same
 * }
 */
class FighterMatcher
{
    private array $aliases = [
        '정찬성' => ['chan sung jung', 'jung chan sung', 'korean zombie'],
        '김동현' => ['dong hyun kim', 'stun gun'],
    ];

    /** Determines if two FighterDTOs represent the same fighter based on their Sherdog ID or similar names.
     *
     * @param FighterDTO $a The first fighter to compare.
     * @param FighterDTO $b The second fighter to compare.
     * @return bool True if the fighters are considered the same, false otherwise.
     */
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

    /** Picks the non-null value between two options, preferring the second option if both are non-null.
     *
     * @param mixed $a The first value to compare.
     * @param mixed $b The second value to compare.
     * @return mixed The chosen value, which is the second option if it's non-null, otherwise the first option.
     */
    private function matchBySherdogId(FighterDTO $a, FighterDTO $b): bool
    {
        return $a->sherdogId !== null
            && $b->sherdogId !== null
            && $a->sherdogId === $b->sherdogId;
    }

    /** Determines if two fighter names match based on a predefined alias dictionary.
     *
     * @param string $a The first fighter name to compare.
     * @param string $b The second fighter name to compare.
     * @return bool True if the names match based on the alias dictionary, false otherwise.
     */
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

    /** Determines if two fighter names are similar enough to be considered the same fighter using fuzzy matching.
     *
     * @param string $a The first fighter name to compare.
     * @param string $b The second fighter name to compare.
     * @return bool True if the names are considered similar based on fuzzy matching, false otherwise.
     */
    private function matchByFuzzy(string $a, string $b): bool
    {
        $a = $this->normalize($a);
        $b = $this->normalize($b);

        similar_text($a, $b, $percent);

        return $percent > 85;
    }

    /** Normalizes a fighter's name by converting it to lowercase and removing spaces and non-alphanumeric characters.
     *
     * @param string $name The fighter's name to normalize.
     * @return string The normalized fighter name.
     */
    private function normalize(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);

        $parts = explode(' ', $name);
        sort($parts);

        return implode(' ', $parts);
    }
}
