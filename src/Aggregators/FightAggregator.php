<?php

namespace Cable8mm\MmaScrapers\Aggregators;

use Cable8mm\MmaScrapers\DTO\FightDTO;
use Cable8mm\MmaScrapers\DTO\FighterDTO;
use Cable8mm\MmaScrapers\Enums\Source;

/**
 * The FightAggregator is responsible for merging two FightDTOs that represent the same fight.
 * It uses a FighterAggregator to merge the fighter information for both the red and blue corners.
 * The merge function combines two FightDTOs into one, preferring non-null values and resolving conflicts.
 * The isSameFight function determines if two FightDTOs represent the same fight based on their fighters.
 *
 * Example usage:
 * $agg = new FightAggregator(new FighterAggregator());
 * $fight1 = new FightDTO(redFighter: new FighterDTO(name: 'Fighter A'), blueFighter: new FighterDTO(name: 'Fighter B'));
 * $fight2 = new FightDTO(redFighter: new FighterDTO(name: 'Fighter A'), blueFighter: new FighterDTO(name: 'Fighter B'));
 * if ($agg->isSameFight($fight1, $fight2)) {
 *     $merged = $agg->merge($fight1, $fight2);
 *     // $merged will have merged fighter information and other details from both fights
 * }
 */
class FightAggregator
{
    /**
     * Constructs a new FightAggregator with a FighterAggregator dependency.
     *
     * @param FighterAggregator $fighterAggregator The FighterAggregator to use for merging fighter information.
     */
    public function __construct(
        private FighterAggregator $fighterAggregator
    ) {
    }

    /**
     * Merges two FightDTOs into one, preferring non-null values and resolving conflicts.
     * Throws an exception if the fights are determined to be different.
     *
     * @param FightDTO $a The first fight to merge.
     * @param FightDTO $b The second fight to merge.
     * @return FightDTO The merged fight.
     * @throws \InvalidArgumentException If the fights are determined to be different.
     */
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
     * Determines if two FightDTOs represent the same fight based on their fighters.
     *
     * @param FightDTO $a The first fight to compare.
     * @param FightDTO $b The second fight to compare.
     * @return bool True if the fights are considered the same, false otherwise.
     */
    public function isSameFight(FightDTO $a, FightDTO $b): bool
    {
        return $this->isSameFighterPair($a, $b);
    }

    /** Determines if two FightDTOs have the same pair of fighters, regardless of corner.
     *
     * @param FightDTO $a The first fight to compare.
     * @param FightDTO $b The second fight to compare.
     * @return bool True if both fights have the same pair of fighters, false otherwise.
     */
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
     * Resolves the winner of the fight based on the information from both FightDTOs and the merged fighter information.
     *
     * @param FightDTO $a The first fight to compare.
     * @param FightDTO $b The second fight to compare.
     * @param FighterDTO $red The merged red corner fighter.
     * @param FighterDTO $blue The merged blue corner fighter.
     * @return FighterDTO|null The resolved winner of the fight, or null if it cannot be determined.
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

    /**
     * Maps a winner from the original FightDTO to the merged FighterDTOs.
     *
     * @param FighterDTO $winner The winner from the original FightDTO.
     * @param FighterDTO $red The merged red corner fighter.
     * @param FighterDTO $blue The merged blue corner fighter.
     * @return FighterDTO|null The corresponding merged fighter who is the winner, or null if it cannot be determined.
     */
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
     * Picks the source for the merged FightDTO, preferring the second FightDTO's source if it's from Sherdog.
     *
     * @param FightDTO $a The first fight to compare.
     * @param FightDTO $b The second fight to compare.
     * @return Source The chosen source for the merged FightDTO.
     */
    private function pickSource(FightDTO $a, FightDTO $b): Source
    {
        // Sherdog 우선
        if ($b->source->value === 'sherdog') {
            return $b->source;
        }

        return $a->source;
    }

    /**
     * Picks the non-null value between two options, preferring the second option if both are non-null.
     *
     * @param mixed $a The first value to compare.
     * @param mixed $b The second value to compare.
     * @return mixed The chosen value, which is the second option if it's non-null, otherwise the first option.
     */
    private function pick(mixed $a, mixed $b): mixed
    {
        return $b ?? $a;
    }

    /**
     * Aligns the fighters of the second FightDTO to match the order of the first FightDTO.
     *
     * @param FightDTO $a The reference fight to align to.
     * @param FightDTO $b The fight to be aligned.
     * @return array An array containing the aligned red and blue fighters from the second FightDTO.
     * @throws \InvalidArgumentException If the fighters cannot be aligned.
     */
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
