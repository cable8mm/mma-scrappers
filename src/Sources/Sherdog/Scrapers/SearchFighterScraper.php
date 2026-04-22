<?php

namespace Cable8mm\MmaScrapers\Sources\Sherdog\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;

class SearchFighterScraper
{
    public function __construct(
        private HttpClientInterface $http
    ) {
    }

    public function search(string $name): string
    {
        $url = 'https://www.sherdog.com/stats/fightfinder?SearchTxt=' . urlencode($name);

        return $this->http->get($url);
    }
}
