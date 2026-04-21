<?php

namespace Cable8mm\MmaScrapers\Contracts;

use Cable8mm\MmaScrapers\DTO\EventDTO;

/**
 * Interface PromotionScraper
 *
 * A contract for scrapers that can scrape events from a specific promotion's website.
 */
interface PromotionScraper
{
    /**
     * Scrape events from the promotion's website and return an array of EventDTO objects.
     *
     * @return EventDTO[]
     */
    public function events(): array;
}
