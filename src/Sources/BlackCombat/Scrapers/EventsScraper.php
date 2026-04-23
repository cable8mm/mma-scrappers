<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseEvents;

/**
 * The EventsScraper class is responsible for scraping a list of events from a given URL and returning an array of EventDTOs.
 * It uses an HTTP client to fetch the HTML content and a parser to extract the relevant information.
 *
 * Example usage:
 * $httpClient = new GuzzleHttpClient();
 * $parser = new ParseEvents();
 * $scraper = new EventsScraper($httpClient, $parser);
 * $events = $scraper->scrape('https://www.blackcombat.com/events');
 * // $events will contain an array of EventDTOs with the scraped event details
 */
class EventsScraper
{
    /**
     * EventsScraper constructor.
     *
     * @param HttpClientInterface $http An HTTP client for fetching HTML content.
     * @param ParseEvents $parser A parser for extracting event details from HTML.
     */
    public function __construct(
        private HttpClientInterface $http,
        private ParseEvents $parser
    ) {
    }

    /**
     * Scrape a list of events from the given URL and return an array of EventDTOs.
     *
     * @param string $url The URL of the events page to scrape.
     * @return EventDTO[] An array of EventDTOs with the scraped event details.
     */
    public function scrape(string $url): array
    {
        $html = $this->http->get($url);

        return ($this->parser)($html);
    }
}
