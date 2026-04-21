<?php

namespace Cable8mm\MmaScrapers\Sources\Sherdog\Parsers;

use Cable8mm\MmaScrapers\DTO\FighterDTO;
use Symfony\Component\DomCrawler\Crawler;

class ParseFighter
{
    public function __invoke(string $html): FighterDTO
    {
        $crawler = new Crawler($html);

        $name = $this->safeText($crawler, 'h1 span.fn');
        $nickname = $this->safeText($crawler, 'h1 span.nickname');

        $height = $this->parseHeight($crawler);
        $team = $this->parseTeams($crawler);

        [$win, $lose, $draw] = $this->parseRecord($crawler);

        return new FighterDTO(
            name: $name,
            nickname: $nickname,
            teamname: $team,
            height: $height,
            win: $win,
            lose: $lose,
            draw: $draw,
            sherdogId: $this->extractSherdogId($crawler)
        );
    }

    private function safeText(Crawler $crawler, string $selector): ?string
    {
        return $crawler->filter($selector)->count()
            ? trim($crawler->filter($selector)->text())
            : null;
    }

    /**
     * HEIGHT → cm 기준 반환
     */
    private function parseHeight(Crawler $crawler): ?string
    {
        $rows = $crawler->filter('.fighter-data table tr');

        foreach ($rows as $row) {
            $node = new Crawler($row);

            if (str_contains($node->text(), 'HEIGHT')) {
                $text = $node->filter('td')->eq(1)->text();

                // cm 우선 사용
                if (preg_match('/([0-9.]+)\s*cm/', $text, $m)) {
                    return (string) round((float)$m[1]);
                }

                // fallback: feet → cm 변환
                if (preg_match("/([0-9]+)'([0-9]+)/", $text, $m)) {
                    $feet = (int)$m[1];
                    $inch = (int)$m[2];

                    return (string) round(($feet * 30.48) + ($inch * 2.54));
                }
            }
        }

        return null;
    }

    /**
     * TEAM (복수 지원)
     */
    private function parseTeams(Crawler $crawler): ?string
    {
        $teams = $crawler
            ->filter('.association-class a.association span[itemprop="name"]')
            ->each(fn ($node) => trim($node->text()));

        return $teams ? implode(', ', $teams) : null;
    }

    /**
     * Wins / Losses
     */
    private function parseRecord(Crawler $crawler): array
    {
        $win = $this->extractNumber(
            $crawler,
            '.wins .winloses span:nth-child(2)'
        );

        $lose = $this->extractNumber(
            $crawler,
            '.loses .winloses span:nth-child(2)'
        );

        return [$win, $lose, 0]; // Sherdog은 draw 거의 없음
    }

    private function extractNumber(Crawler $crawler, string $selector): ?int
    {
        if (!$crawler->filter($selector)->count()) {
            return null;
        }

        return (int) trim($crawler->filter($selector)->text());
    }

    private function extractSherdogId(Crawler $crawler): ?int
    {
        if (!$crawler->filter('link[rel="next"]')->count()) {
            return null;
        }

        $url = $crawler->filter('link[rel="next"]')->attr('href');

        preg_match('/-([0-9]+)$/', $url, $m);

        return isset($m[1]) ? (int)$m[1] : null;
    }
}
