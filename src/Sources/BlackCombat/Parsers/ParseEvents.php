<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers;

use Cable8mm\MmaScrapers\DTO\EventDTO;
use Symfony\Component\DomCrawler\Crawler;

class ParseEvents
{
    public function __invoke(string $html): array
    {
        $crawler = new Crawler($html);

        $events = [];

        $crawler->filter('.event_list li')->each(function (Crawler $node) use (&$events) {

            $lines = $node->filter('div > div > div');

            $name = trim($lines->eq(0)->text());

            $date = trim($lines->eq(1)->text()); // 2026년 01월 31일
            $date = str_replace('년', '-', $date);
            $date = str_replace('월', '-', $date);
            $date = str_replace('일', '', $date);
            $date = str_replace(' ', '', $date);

            $date = new \DateTimeImmutable($date);

            $location = trim($lines->eq(2)->text());

            $url = $node->filter('button')->attr('onclick');

            $url = str_replace('location.href=', '', $url);
            $url = str_replace('"', '', $url);
            $url = str_replace("';", '', $url);

            $events[] = new EventDTO(
                $name,
                $location,
                $date,
                $url
            );
        });

        return $events;
    }
}
