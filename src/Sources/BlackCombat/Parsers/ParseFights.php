<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers;

use Cable8mm\MmaScrapers\DTO\FightDTO;
use Cable8mm\MmaScrapers\DTO\FighterDTO;
use Cable8mm\MmaScrapers\Enums\FightMethod;
use Cable8mm\MmaScrapers\Enums\FightStatus;
use Cable8mm\MmaScrapers\Enums\Source;
use Cable8mm\MmaScrapers\Normalizers\WeightClassNormalizer;
use Symfony\Component\DomCrawler\Crawler;

class ParseFights
{
    public function __invoke(string $html): array
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
}
