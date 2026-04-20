<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFights;

class FightsScraper
{
    public function __construct(
        private HttpClientInterface $http,
        private ParseFights $parser
    ) {
    }

    public function scrape(string $url): array
    {
        $html = $this->http->get($url);

        return ($this->parser)($html);
    }
}
