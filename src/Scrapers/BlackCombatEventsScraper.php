<?php

namespace Cable8mm\MmaScrapers\Scrapers;

use Cable8mm\MmaScrapers\Contracts\HttpClientInterface;
use Cable8mm\MmaScrapers\Contracts\Scraper;
use Cable8mm\MmaScrapers\Parsers\BlackCombatParser;

class BlackCombatEventsScraper implements Scraper
{
    private const EVENTS_URL = 'https://www.blackcombat-official.com/event.php';

    public function __construct(
        private HttpClientInterface $http,
        private BlackCombatParser $parser
    ) {
    }

    /**
     * @inheritDoc
     */
    public function scrape(?string $url = self::EVENTS_URL): array
    {
        $html = $this->http->get($url);

        return $this->parser->parseEvents($html);
    }
}
