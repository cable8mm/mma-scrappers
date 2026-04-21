<?php

namespace Cable8mm\MmaScrapers\Contracts;

use Cable8mm\MmaScrapers\DTO\EventDTO;
use Cable8mm\MmaScrapers\DTO\FightDTO;

/**
 * Interface EventParserInterface
 *
 * A contract for parsers that can parse event and fight information from HTML content.
 */
interface EventParserInterface
{
    /**
     * Parse multiple events from the given HTML and return an array of EventDTO objects.
     *
     * @param string $html The HTML content to parse.
     * @return EventDTO[]
     */
    public function parseEvents(string $html): array;

    /**
     * Parse a single event from the given HTML and return an EventDTO object.
     *
     * @param string $html The HTML content to parse.
     * @return EventDTO
     */
    public function parseEvent(string $html): EventDTO;

    /**
     * Parse fights from the given HTML and return an array of FightDTO objects.
     *
     * @param string $html The HTML content to parse.
     * @return FightDTO[]
     */
    public function parseFights(string $html): array;
}
