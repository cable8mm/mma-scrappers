<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers;

use Cable8mm\MmaScrapers\DTO\EventDTO;
use Symfony\Component\DomCrawler\Crawler;

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
