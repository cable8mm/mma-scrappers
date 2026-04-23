<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers;

use Cable8mm\MmaScrapers\DTO\EventDTO;
use Symfony\Component\DomCrawler\Crawler;

/**
 * The ParseEvent class is responsible for parsing event details from HTML and returning an EventDTO.
 *
 * It uses the Symfony DomCrawler to extract relevant information from the HTML structure.
 *
 * Example usage:
 * $html = file_get_contents('event_detail.html');
 * $parser = new ParseEvent();
 * $eventDTO = $parser($html);
 * // $eventDTO will contain the parsed event details
 */
class ParseEvent
{
    /**
     * Parse event details from HTML and return an EventDTO.
     *
     * @param string $html
     * @return EventDTO
     */
    public function __invoke(string $html): EventDTO
    {
        $crawler = new Crawler($html);

        $event = new EventDTO(
            name: $crawler->filter('div.category')->text()
        );

        return $event;
    }
}
