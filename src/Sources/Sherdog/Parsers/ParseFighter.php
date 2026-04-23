<?php

namespace Cable8mm\MmaScrapers\Sources\Sherdog\Parsers;

use Cable8mm\MmaScrapers\DTO\FighterDTO;
use Symfony\Component\DomCrawler\Crawler;

/**
 * The ParseFighter class is responsible for parsing fighter details from HTML and returning a FighterDTO.
 *
 * It uses the Symfony DomCrawler to extract relevant information from the HTML structure.
 *
 * Example usage:
 * $html = file_get_contents('fighter_detail.html');
 * $parser = new ParseFighter();
 * $fighterDTO = $parser($html);
 * // $fighterDTO will contain the parsed fighter details
 */
class ParseFighter
{
    /**
     * Parse fighter details from HTML and return a FighterDTO.
     *
     * @param string $html
     * @return FighterDTO
     */
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

    /**
     * Safely extract text from the crawler using the given selector.
     * Returns null if the selector does not match any elements.
     */
    private function safeText(Crawler $crawler, string $selector): ?string
    {
        return $crawler->filter($selector)->count()
            ? trim($crawler->filter($selector)->text())
            : null;
    }

    /**
     * Parse the fighter's height from the HTML.
     * Supports both cm and feet/inch formats, with a preference for cm.
     *
     * @param Crawler $crawler
     * @return string|null The height in cm, or null if it cannot be parsed.
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
     * Parse the fighter's team affiliations from the HTML.
     * Returns a comma-separated string of team names, or null if no teams are found.
     *
     * @param Crawler $crawler
     * @return string|null
     */
    private function parseTeams(Crawler $crawler): ?string
    {
        $teams = $crawler
            ->filter('.association-class a.association span[itemprop="name"]')
            ->each(fn ($node) => trim($node->text()));

        return $teams ? implode(', ', $teams) : null;
    }

    /**
     * Parse the fighter's win/loss/draw record from the HTML.
     * Returns an array with the number of wins, losses, and draws.
     *
     * @param Crawler $crawler
     * @return array An array containing [win, lose, draw] counts.
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

    /**
     * Extract a numeric value from the specified selector in the crawler.
     * Returns null if the selector does not match or if the text cannot be parsed as a number.
     *
     * @param Crawler $crawler
     * @param string $selector
     * @return int|null
     */
    private function extractNumber(Crawler $crawler, string $selector): ?int
    {
        if (!$crawler->filter($selector)->count()) {
            return null;
        }

        return (int) trim($crawler->filter($selector)->text());
    }

    /**
     * Extract the Sherdog ID from the HTML.
     * Returns the ID as an integer, or null if it cannot be found.
     *
     * @param Crawler $crawler
     * @return int|null
     */
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
