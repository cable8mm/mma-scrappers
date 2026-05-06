<?php

namespace Cable8mm\MmaScrapers\DTO;

use DateTimeInterface;
use Transliterator;

/**
 * Data Transfer Object representing an event.
 */
class EventDTO
{
    /**
     * EventDTO constructor.
     *
     * @param string $name The name of the event.
     * @param string|null $nameEn The English name of the event, if available.
     * @param string|null $location The location where the event takes place.
     * @param \DateTimeInterface|null $date The date of the event.
     * @param string|null $url The URL to the event's webpage.
     * @param string|null $externalId An optional external identifier for the event, such as an ID from a third-party service.
     */
    public function __construct(
        public string $name,
        public ?string $nameEn = null,
        public ?string $location = null,
        public ?DateTimeInterface $date = null,
        public ?string $url = null,
        public ?string $externalId = null
    ) {
        if (empty($this->nameEn)) {
            $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');
            $this->nameEn = $transliterator->transliterate($this->name);
        }
    }
}
