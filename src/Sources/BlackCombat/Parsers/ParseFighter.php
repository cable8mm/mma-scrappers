<?php

namespace Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers;

use Cable8mm\MmaScrapers\DTO\FighterDTO;
use Symfony\Component\DomCrawler\Crawler;

class ParseFighter
{
    public function __invoke(string $html): FighterDTO
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
