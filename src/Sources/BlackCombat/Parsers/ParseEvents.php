<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers;

use Cable8mm\MmaScrapers\DTO\EventDTO;
use DateTime;
use Symfony\Component\DomCrawler\Crawler;

/**
 * The ParseEvents class is responsible for parsing a list of events from HTML and returning an array of EventDTOs.
 *
 * It uses the Symfony DomCrawler to extract relevant information from the HTML structure.
 *
 * Example usage:
 * $html = file_get_contents('events_list.html');
 * $parser = new ParseEvents();
 * $events = $parser($html);
 * // $events will contain an array of EventDTOs with the parsed event details
 */
class ParseEvents
{
    /**
     * Parse a list of events from HTML and return an array of EventDTOs.
     *
     * @param string $html
     * @return EventDTO[]
     */
    public function __invoke(string $html): array
    {
        $crawler = new Crawler($html);

        $events = [];

        $crawler->filter('.event_list li')->each(function (Crawler $node) use (&$events) {

            $lines = $node->filter('div > div > div');

            $name = trim($lines->eq(0)->text());

            $date = trim($lines->eq(1)->text()); // 2026년 01월 31일
            $date = str_replace('년', '-', $date);
            $date = str_replace('월', '-', $date);
            $date = str_replace('일', '', $date);
            $date = str_replace(' ', '', $date);

            $location = trim($lines->eq(2)->text());

            $url = $node->filter('button')->attr('onclick');

            $url = str_replace('location.href=', '', $url);
            $url = str_replace('"', '', $url);
            $url = str_replace("';", '', $url);

            $events[] = new EventDTO(
                name: $name,
                location: $location,
                date: new DateTime($date),
                url: $url
            );
        });

        return $events;
    }
}
