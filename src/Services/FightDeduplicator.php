<?php

namespace Cable8mm\MmaScrapers\Services;

use Cable8mm\MmaScrapers\DTO\FightDTO;
use Cable8mm\MmaScrapers\Aggregators\FightAggregator;

/**
 * The FightDeduplicator is responsible for deduplicating a list of FightDTOs by grouping them based on their similarity and merging them using a FightAggregator.
 * The group function groups fights that are considered the same based on the FightAggregator's isSameFight method.
 * The deduplicate function merges each group of similar fights into a single FightDTO using the FightAggregator's merge method.
 *
 * Example usage:
 * $agg = new FightAggregator(new FighterAggregator());
 * $dedup = new FightDeduplicator($agg);
 * $fights = [...]; // An array of FightDTOs
 * $result = $dedup->deduplicate($fights);
 * // $result will contain deduplicated and merged FightDTOs
 */
class FightDeduplicator
{
    /**
     * Constructs a new FightDeduplicator with a FightAggregator dependency.
     *
     * @param FightAggregator $aggregator The FightAggregator to use for merging fights.
     */
    public function __construct(
        private FightAggregator $aggregator
    ) {
    }

    /** Groups fights that are considered the same based on the FightAggregator's isSameFight method.
     *
     * @param FightDTO[] $fights An array of FightDTOs to group.
     * @return array An array of groups, where each group is an array of FightDTOs that are considered the same fight.
     */
    public function group(array $fights): array
    {
        $groups = [];

        foreach ($fights as $fight) {

            $found = false;

            foreach ($groups as &$group) {

                if ($this->aggregator->isSameFight($group[0], $fight)) {
                    $group[] = $fight;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $groups[] = [$fight];
            }
        }

        return $groups;
    }

    /** Deduplicates a list of FightDTOs by grouping them based on their similarity and merging them using a FightAggregator.
    *
    * @param FightDTO[] $fights An array of FightDTOs to deduplicate.
    * @return FightDTO[] An array of deduplicated and merged FightDTOs.
    */
    public function deduplicate(array $fights): array
    {
        $groups = $this->group($fights);

        return array_map(function ($group) {

            $merged = array_shift($group);

            foreach ($group as $fight) {
                $merged = $this->aggregator->merge($merged, $fight);
            }

            return $merged;

        }, $groups);
    }
}
