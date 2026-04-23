<?php

namespace Cable8mm\MmaScrapers\Sources\Sherdog\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\Sherdog\Parsers\ParseFighter;
use Cable8mm\MmaScrapers\DTO\FighterDTO;

/**
 * The FighterScraper class is responsible for scraping fighter details from a given URL and returning a FighterDTO.
 *
 * It uses an HTTP client to fetch the HTML content and a parser to extract the relevant information.
 *
 * Example usage:
 * $httpClient = new GuzzleHttpClient();
 * $parser = new ParseFighter();
 * $scraper = new FighterScraper($httpClient, $parser);
 * $fighterDTO = $scraper->scrapeById(123);
 * // $fighterDTO will contain the scraped fighter details for the fighter with ID 123
 */
class FighterScraper
{
    /**
     * FighterScraper constructor.
     *
     * @param HttpClientInterface $http An HTTP client for fetching HTML content.
     * @param ParseFighter $parser A parser for extracting fighter details from HTML.
     */
    public function __construct(
        private HttpClientInterface $http,
        private ParseFighter $parser
    ) {
    }

    /**
     * Scrape fighter details by Sherdog ID and return a FighterDTO.
     *
     * @param int $id The Sherdog ID of the fighter to scrape.
     * @return FighterDTO The scraped fighter details as a FighterDTO.
     */
    public function scrapeById(int $id): FighterDTO
    {
        $url = "https://www.sherdog.com/fighter/-{$id}";

        $html = $this->http->get($url);

        return ($this->parser)($html);
    }
}
