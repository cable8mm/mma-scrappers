<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\DTO\EventDTO;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseEvent;

/**
 * The EventScraper class is responsible for scraping event details from a given URL and returning an EventDTO.
 * It uses an HTTP client to fetch the HTML content and a parser to extract the relevant information.
 *
 * Example usage:
 * $httpClient = new GuzzleHttpClient();
 * $parser = new ParseEvent();
 * $scraper = new EventScraper($httpClient, $parser);
 * $eventDTO = $scraper->scrape('https://www.blackcombat.com/event/123');
 * // $eventDTO will contain the scraped event details
 */
class EventScraper
{
    /**
     * EventScraper constructor.
     *
     * @param HttpClientInterface $http An HTTP client for fetching HTML content.
     * @param ParseEvent $parser A parser for extracting event details from HTML.
     */
    public function __construct(
        private HttpClientInterface $http,
        private ParseEvent $parser
    ) {
    }

    /**
     * Scrape event details from the given URL and return an EventDTO.
     *
     * @param string $url The URL of the event page to scrape.
     * @return EventDTO The scraped event details as an EventDTO.
     */
    public function scrape(string $url): EventDTO
    {
        $html = $this->http->get($url);

        return ($this->parser)($html);
    }
}
