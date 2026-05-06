<?php

namespace Cable8mm\MmaScrapers\DTO;

use Transliterator;

/**
 * Data Transfer Object representing a fighter.
 */
class FighterDTO
{
    /**
    * FighterDTO constructor.
    *
    * @param string $name The name of the fighter.
    * @param string|null $nameEn The English name of the fighter, if available.
    * @param string|null $nickname The fighter's nickname, if available.
    * @param string|null $instagram The fighter's Instagram handle, if available.
    * @param string|null $teamname The name of the fighter's team or gym, if available.
    * @param string|null $height The fighter's height, if available.
    * @param int|null $win The number of wins the fighter has, if available.
    * @param int|null $lose The number of losses the fighter has, if available.
    * @param int|null $draw The number of draws the fighter has, if available.
    * @param int|null $sherdogId The fighter's Sherdog ID, if available.
    */
    public function __construct(
        public string $name,
        public ?string $nameEn = null,
        public ?string $nickname = null,
        public ?string $instagram = null,
        public ?string $teamname = null,
        public ?string $height = null,
        public ?int $win = null,
        public ?int $lose = null,
        public ?int $draw = null,
        public ?int $sherdogId = null,
    ) {
        if (empty($this->nameEn)) {
            $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');
            $this->nameEn = $transliterator->transliterate($this->name);
        }
    }
}
