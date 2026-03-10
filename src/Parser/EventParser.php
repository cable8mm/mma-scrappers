<?php

namespace Cable8mm\MmaScrapers\Parser;

use Cable8mm\MmaScrapers\DTO\Event;
use Symfony\Component\DomCrawler\Crawler;

class EventParser
{
    public function parse(string $html): Event
    {
        $crawler = new Crawler($html);

        $event = new Event();

        $event->name = $crawler->filter('h1')->text();

        return $event;
    }
}
