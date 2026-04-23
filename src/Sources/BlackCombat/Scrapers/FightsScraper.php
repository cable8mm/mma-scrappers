<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFights;

/**
 * The FightsScraper class is responsible for scraping fight details from a given URL and returning an array of FightDTOs.
 *
 * It uses an HTTP client to fetch the HTML content and a parser to extract the relevant information.
 *
 * Example usage:
 * $httpClient = new GuzzleHttpClient();
 * $parser = new ParseFights();
 * $scraper = new FightsScraper($httpClient, $parser);
 * $fights = $scraper->scrape('https://www.blackcombat.com/event/123/fights');
 * // $fights will contain an array of FightDTOs with the scraped fight details
 */
class FightsScraper
{
    /**
     * FightsScraper constructor.
     *
     * @param HttpClientInterface $http An HTTP client for fetching HTML content.
     * @param ParseFights $parser A parser for extracting fight details from HTML.
     */
    public function __construct(
        private HttpClientInterface $http,
        private ParseFights $parser
    ) {
    }

    /**
     * Scrape fight details from the given URL and return an array of FightDTOs.
     *
     * @param string $url The URL of the fights page to scrape.
     * @return FightDTO[] An array of FightDTOs with the scraped fight details.
     */
    public function scrape(string $url): array
    {
        $html = $this->http->get($url);

        return ($this->parser)($html);
    }
}
