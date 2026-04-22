<?php

namespace Cable8mm\MmaScrapers\Sources\Sherdog\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class ParseSearchResults
{
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
