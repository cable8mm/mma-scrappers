<?php

namespace Cable8mm\MmaScrapers\Services;

use Cable8mm\MmaScrapers\DTO\FightDTO;
use Cable8mm\MmaScrapers\Aggregators\FightAggregator;

class FightDeduplicator
{
    public function __construct(
        private FightAggregator $aggregator
    ) {
    }

    /**
     * 그룹화 (같은 경기끼리 묶기)
     *
     * @param FightDTO[] $fights
     * @return array<int, FightDTO[]>
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

    /**
     * deduplicate (merge까지 수행)
     *
     * @param FightDTO[] $fights
     * @return FightDTO[]
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
