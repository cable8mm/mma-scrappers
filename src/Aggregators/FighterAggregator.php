<?php

namespace Cable8mm\MmaScrapers\Aggregators;

use Cable8mm\MmaScrapers\DTO\FighterDTO;

/**
 * The FighterAggregator is responsible for merging two FighterDTOs that represent the same fighter.
 * The merge function combines two FighterDTOs into one, preferring non-null values and resolving conflicts.
 * The isSameFighter function determines if two FighterDTOs represent the same fighter based on their Sherdog ID or similar names.
 *
 * Example usage:
 * $agg = new FighterAggregator();
 * $fighter1 = new FighterDTO(name: 'Fighter A', sherdogId: 123);
 * $fighter2 = new FighterDTO(name: 'Fighter A', sherdogId: 123);
 * if ($agg->isSameFighter($fighter1, $fighter2)) {
 *     $merged = $agg->merge($fighter1, $fighter2);
 *     // $merged will have merged information from both fighters
 * }
 */
class FighterAggregator
{
    /**
     * Merges two FighterDTOs into one, preferring non-null values and resolving conflicts.
     * Throws an exception if the fighters are determined to be different.
     *
     * @param FighterDTO $a The first fighter to merge.
     * @param FighterDTO $b The second fighter to merge.
     * @return FighterDTO The merged fighter.
     * @throws \InvalidArgumentException If the fighters are determined to be different.
     */
    public function merge(FighterDTO $a, FighterDTO $b): FighterDTO
    {
        if (!$this->isSameFighter($a, $b)) {
            throw new \InvalidArgumentException('Different fighters');
        }

        return new FighterDTO(
            name: $this->pick($a->name, $b->name),
            nickname: $this->pick($a->nickname, $b->nickname),
            instagram: $this->pick($a->instagram, $b->instagram),
            teamname: $this->pick($a->teamname, $b->teamname),
            height: $this->pick($a->height, $b->height),
            win: $this->pick($a->win, $b->win),
            lose: $this->pick($a->lose, $b->lose),
            draw: $this->pick($a->draw, $b->draw),
            sherdogId: $this->pick($a->sherdogId, $b->sherdogId),
        );
    }

    /**
     * Determines if two FighterDTOs represent the same fighter based on their Sherdog ID or similar names.
     *
     * @param FighterDTO $a The first fighter to compare.
     * @param FighterDTO $b The second fighter to compare.
     * @return bool True if the fighters are considered the same, false otherwise.
     */
    public function isSameFighter(FighterDTO $a, FighterDTO $b): bool
    {
        if ($a->sherdogId && $b->sherdogId) {
            return $a->sherdogId === $b->sherdogId;
        }

        return $this->isSimilarName($a->name, $b->name);
    }

    /** Picks the non-null value between two options, preferring the second option if both are non-null.
     *
     * @param mixed $a The first value to compare.
     * @param mixed $b The second value to compare.
     * @return mixed The chosen value, which is the second option if it's non-null, otherwise the first option.
     */
    private function pick(mixed $a, mixed $b): mixed
    {
        return $b ?? $a;
    }

    /** Determines if two names are similar enough to be considered the same fighter.
     *
     * @param string $a The first name to compare.
     * @param string $b The second name to compare.
     * @return bool True if the names are considered similar, false otherwise.
     */
    private function isSimilarName(string $a, string $b): bool
    {
        $a = $this->normalizeName($a);
        $b = $this->normalizeName($b);

        if ($a === $b) {
            return true;
        }

        if (str_contains($a, $b) || str_contains($b, $a)) {
            return true;
        }

        return levenshtein($a, $b) <= 2;
    }

    /**
     * Normalizes a fighter's name by converting it to lowercase and removing spaces and non-alphanumeric characters.
     *
     * @param string $name The fighter's name to normalize.
     * @return string The normalized fighter name.
     */
    private function normalizeName(string $name): string
    {
        $name = mb_strtolower($name);

        $name = preg_replace('/\s+/', '', $name);

        $name = preg_replace('/[^a-z0-9가-힣]/u', '', $name);

        return $name;
    }
}
