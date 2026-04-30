<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseEvents;

/**
 * The EventsScraper class is responsible for scraping a list of events from a given URL and returning an array of EventDTOs.
 *
 * It uses an HTTP client to fetch the HTML content and a parser to extract the relevant information.
 *
 * Example usage:
 * $httpClient = new DefaultHttpClient();
 * $parser = new ParseEvents();
 * $scraper = new EventsScraper($httpClient, $parser);
 * $events = $scraper->scrape('https://www.blackcombat.com/events');
 * // $events will contain an array of EventDTOs with the scraped event details
 */
class EventsScraper
{
    private const URL = 'https://www.blackcombat-official.com/event.php';

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
     * If no URL is provided, it defaults to the predefined URL for events.
     * The method fetches the HTML content from the URL and uses the parser to extract event details, returning them as an array of EventDTOs.
     *
     * Note: The actual structure of EventDTOs and the parsing logic will depend on the implementation of the ParseEvents parser.
     * Make sure to handle any exceptions or errors that may occur during the HTTP request or parsing process as needed.
     *
     * Example usage:
     * $events = $scraper->scrape(); // Scrapes from the default URL
     * $events = $scraper->scrape('https://www.blackcombat.com/custom-events'); // Scrapes from a custom URL
     *
     * @param string|null $url The URL of the events page to scrape. If null, the default URL will be used.
     * @return EventDTO[] An array of EventDTOs with the scraped event details.
     */
    public function scrape(?string $url = null): array
    {
        $url = $url ?? self::URL;

        $html = $this->http->get($url);

        return ($this->parser)($html);
    }
}
