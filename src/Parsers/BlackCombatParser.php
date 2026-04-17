<?php

namespace Cable8mm\MmaScrapers\Parsers;

use Cable8mm\MmaScrapers\Contracts\EventParserInterface;
use Cable8mm\MmaScrapers\DTO\EventDTO;
use Cable8mm\MmaScrapers\DTO\FightDTO;
use Cable8mm\MmaScrapers\DTO\FighterDTO;
use Cable8mm\MmaScrapers\Enums\FightMethod;
use Cable8mm\MmaScrapers\Enums\FightStatus;
use Cable8mm\MmaScrapers\Enums\Source;
use Cable8mm\MmaScrapers\Normalizers\WeightClassNormalizer;
use Symfony\Component\DomCrawler\Crawler;

class BlackCombatParser implements EventParserInterface
{
    /**
     * Parse the HTML content and extract event information.
     *
     * @param string $html The HTML content to parse.
     * @return EventDTO[]
     */
    public function parseEvents(string $html): array
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

    /**
     * Parse the HTML content and extract detailed event information.
     *
     * @param string $html The HTML content to parse.
     * @return EventDTO The detailed event information.
     */
    public function parseEvent(string $html): EventDTO
    {
        $crawler = new Crawler($html);

        $event = new EventDTO(
            name: $crawler->filter('div.category')->text()
        );

        return $event;
    }

    /**
     * Parse the HTML content and extract fight information.
     *
     * @param string $html The HTML content to parse.
     * @return FightDTO[] The list of fights.
     */
    public function parseFights(string $html): array
    {
        $crawler = new Crawler($html);

        $fights = [];

        $crawler->filter('#container > div.sub_content > div > div > div > div:nth-child(2) > div')->each(function (Crawler $node) use (&$fights) {

            $red = new FighterDTO(trim($node->filter('div:nth-child(2) > div:nth-child(2) > div:nth-child(1) > span')->text()));
            $blue = new FighterDTO(trim($node->filter('div:nth-child(2) > div:nth-child(2) > div:nth-child(3) > span')->text()));

            $winner = null;
            if ($node->matches('div:nth-child(1) > a > div')) {
                $winner = $red;
            }

            if ($node->matches('div:nth-child(3) > a > div')) {
                $winner = $blue;
            }

            $status = $winner ? FightStatus::FINISHED : FightStatus::SCHEDULED;

            $weightText = $node->filter('div:nth-child(2) > div:nth-child(5)')->text();

            $scoreCardHref = $node->filter('div:nth-child(2) > div:nth-child(4) > div:nth-child(2) > a')->attr('href');

            $method = preg_match('/null/', $scoreCardHref) ? FightMethod::KO : FightMethod::DECISION;

            $round = $node->filter('.round')->count()
                ? (int) $node->filter('.round')->text()
                : null;

            $time = $node->filter('.time')->count()
                ? $node->filter('.time')->text()
                : null;

            if ($node->filter('.fighter.red.winner')->count()) {
                $winner = $red;
            } elseif ($node->filter('.fighter.blue.winner')->count()) {
                $winner = $blue;
            }

            $fights[] = new FightDTO(
                redFighter: $red,
                blueFighter: $blue,
                source: Source::OFFICIAL,
                status: $status,
                weightClass: $weightText
                    ? WeightClassNormalizer::normalize($weightText)
                    : null,
                method: $method,
                round: $round,
                time: $time,
                winner: $winner
            );
        });

        return $fights;
    }

    public function parseFighter(string $html): FighterDTO
    {
        $crawler = new Crawler($html);

        // "김명환 "맘모스" AGE : 28"
        $fighterInfo = $crawler->filter('#container > div.sub_content.fighter > div > div > div > div.fighter_data > div.data_name')->text();

        $name = trim(explode(' ', $fighterInfo)[0]);
        $nickname = preg_replace('/.*"(.*)".*/', '$1', $fighterInfo);

        $instagram = $crawler->filter('#container > div.sub_content.fighter > div > div > div > div.fighter_data > div.sns_link > a')->text();

        $teamname = $crawler->filter('#container > div.sub_content.fighter > div > div > div > div.fighter_data > div.data_team > b > a')->text();

        $heightText = $crawler->filter('#container > div.sub_content.fighter > div > div > div > div.fighter_data > div.data_bio > div.data_bio_height')->text();
        $height = preg_replace('/[^0-9]/', '', $heightText);

        // "10 Win 4 Loss 0 Draw"
        $wldText = $crawler->filter('#container > div.sub_content.fighter > div > div > div > div.fighter_data > div.data_record')->text();

        $win = preg_replace('/([0-9]+) Win.*/', '$1', $wldText);
        $lose = preg_replace('/.+([0-9]+) Loss.*/', '$1', $wldText);
        $draw = preg_replace('/.*([0-9]+) Draw.*/', '$1', $wldText);

        // "https://www.sherdog.com/fighter/Myung-Hwan-Kim-292151"
        $sherdogLink = $crawler->filter('#container > div.sub_content.fighter > div > div > div > div.fighter_data > div:nth-child(8) > div > a')->attr('href');

        $sherdogId = preg_replace('/.*\-([0-9]+)/', '$1', $sherdogLink);

        $fighter = new FighterDTO(
            name: $name,
            nickname: $nickname,
            instagram: $instagram,
            teamname: $teamname,
            height: $height,
            win: is_numeric($win) ? (int) $win : null,
            lose: is_numeric($lose) ? (int) $lose : null,
            draw: is_numeric($draw) ? (int) $draw : null,
            sherdogId: $sherdogId
        );

        return $fighter;
    }
}
