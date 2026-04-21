<?php

namespace Cable8mm\MmaScrapers\Sources\Sherdog\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Sources\Sherdog\Parsers\ParseFighter;
use Cable8mm\MmaScrapers\DTO\FighterDTO;

class FighterScraper
{
    public function __construct(
        private HttpClientInterface $http,
        private ParseFighter $parser
    ) {
    }

    public function scrapeById(int $id): FighterDTO
    {
        $url = "https://www.sherdog.com/fighter/-{$id}";

        $html = $this->http->get($url);

        return ($this->parser)($html);
    }
}
