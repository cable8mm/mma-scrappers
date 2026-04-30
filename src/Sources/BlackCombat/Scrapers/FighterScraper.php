<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\DTO\FighterDTO;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFighter;

/**
 * The FighterScraper class is responsible for scraping fighter details from a given URL and returning a FighterDTO.
 *
 * It uses an HTTP client to fetch the HTML content and a parser to extract the relevant information.
 *
 * Example usage:
 * $httpClient = new DefaultHttpClient();
 * $parser = new ParseFighter();
 * $scraper = new FighterScraper($httpClient, $parser);
 * $fighterDTO = $scraper->scrape('https://www.blackcombat.com/fighter/123');
 * // $fighterDTO will contain the scraped fighter details
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
     * Scrape fighter details from the given URL and return a FighterDTO.
     *
     * @param string $url The URL of the fighter page to scrape.
     * @return FighterDTO The scraped fighter details as a FighterDTO.
     */
    public function scrape(string $url): FighterDTO
    {
        $html = $this->http->get($url);

        return ($this->parser)($html);
    }
}
