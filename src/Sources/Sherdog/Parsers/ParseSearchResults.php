<?php

namespace Cable8mm\MmaScrapers\Sources\Sherdog\Parsers;

use Symfony\Component\DomCrawler\Crawler;

/**
 * The ParseSearchResults class is responsible for parsing search results from HTML and returning an array of fighter information.
 *
 * It uses the Symfony DomCrawler to extract relevant information from the HTML structure.
 *
 * Example usage:
 * $html = file_get_contents('search_results.html');
 * $parser = new ParseSearchResults();
 * $results = $parser($html);
 * // $results will contain an array of fighter information with names and IDs
 */
class ParseSearchResults
{
    /**
     * Parse search results from HTML and return an array of fighter information.
     *
     * @param string $html
     * @return array An array of fighter information, each containing 'name' and 'id'.
     */
    public function __invoke(string $html): array
    {
        $crawler = new Crawler($html);

        return $crawler
            ->filter('table tr')
            ->each(function (Crawler $row) {

                if (!$row->filter('a')->count()) {
                    return null;
                }

                $name = trim($row->filter('a')->text());
                $url = $row->filter('a')->attr('href');

                preg_match('/-([0-9]+)$/', $url, $m);

                return [
                    'name' => $name,
                    'id' => isset($m[1]) ? (int)$m[1] : null,
                ];
            });
    }
}
