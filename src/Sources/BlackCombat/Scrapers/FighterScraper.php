<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\DTO\FighterDTO;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFighter;

class FighterScraper
{
    public function __construct(
        private HttpClientInterface $http,
        private ParseFighter $parser
    ) {
    }

    public function scrape(string $url): FighterDTO
    {
        $html = $this->http->get($url);

        return ($this->parser)($html);
    }
}
