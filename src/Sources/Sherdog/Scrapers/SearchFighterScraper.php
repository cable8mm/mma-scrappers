<?php

namespace Cable8mm\MmaScrapers\Sources\Sherdog\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;

/**
 * The SearchFighterScraper class is responsible for scraping search results for fighters from Sherdog based on a given name.
 *
 * It uses an HTTP client to fetch the HTML content of the search results page.
 *
 * Example usage:
 * $httpClient = new GuzzleHttpClient();
 * $scraper = new SearchFighterScraper($httpClient);
 * $searchResultsHtml = $scraper->search('Fighter Name');
 * // $searchResultsHtml will contain the HTML content of the search results page for the given fighter name
 */
class SearchFighterScraper
{
    /**
     * SearchFighterScraper constructor.
     *
     * @param HttpClientInterface $http An HTTP client for fetching HTML content.
     */
    public function __construct(
        private HttpClientInterface $http
    ) {
    }

    /**
     * Scrape search results for fighters from Sherdog based on the given name.
     *
     * @param string $name The name of the fighter to search for.
     * @return string The HTML content of the search results page for the given fighter name.
     */
    public function search(string $name): string
    {
        $url = 'https://www.sherdog.com/stats/fightfinder?SearchTxt=' . urlencode($name);

        return $this->http->get($url);
    }
}
