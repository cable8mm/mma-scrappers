<?php

namespace Cable8mm\MmaScrapers\DTO;

use Cable8mm\MmaScrapers\Enums\FightMethod;
use Cable8mm\MmaScrapers\Enums\FightStatus;
use Cable8mm\MmaScrapers\Enums\Source;
use Cable8mm\MmaScrapers\Enums\WeightClass;

/**
 * Data Transfer Object representing a fight.
 */
class FightDTO
{
    /**
     * FightDTO constructor.
     *
     * @param FighterDTO $redFighter Red Fighter information.
     * @param FighterDTO $blueFighter Blue Fighter information.
     * @param Source $source The source from which the fight data was scraped.
     * @param FightStatus|null $status The status of the fight (e.g., scheduled, completed).
     * @param WeightClass|null $weightClass The weight class of the fight (e.g., Lightweight, Middleweight).
     * @param FightMethod|null $method The method of victory (e.g., KO, Submission).
     * @param int|null $round The round in which the fight ended.
     * @param string|null $time The time at which the fight ended in the final round.
     * @param FighterDTO|null $winner The winner of the fight, if applicable.
     * @param \DateTimeImmutable|null $fightDate The date of the fight.
     */
    public function __construct(
        public FighterDTO $redFighter,
        public FighterDTO $blueFighter,
        public Source $source,
        public ?FightStatus $status = null,
        public ?WeightClass $weightClass = null,
        public ?FightMethod $method = null,
        public ?int $round = null,
        public ?string $time = null,
        public ?FighterDTO $winner = null,
        public ?\DateTimeImmutable $fightDate = null
    ) {
    }
}
