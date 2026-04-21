<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\DTO\EventDTO;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseEvent;

class EventScraper
{
    public function __construct(
        private HttpClientInterface $http,
        private ParseEvent $parser
    ) {
    }

    public function scrape(string $url): EventDTO
    {
        $html = $this->http->get($url);

        return ($this->parser)($html);
    }
}
