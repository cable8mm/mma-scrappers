<?php

namespace Cable8mm\MmaScrapers\Contracts;

/**
 * Interface Scraper
 *
 * A generic interface for scrapers that can scrape data from a given URL and return an iterable of results.
 */
interface Scraper
{
    /**
     * Scrape data from the given URL and return an iterable of results.
     *
     * @param string|null $url The URL to scrape data from. If null, the scraper may use a default URL or configuration.
     * @return iterable
     */
    public function scrape(?string $url): iterable;
}
