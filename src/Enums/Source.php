<?php

namespace Cable8mm\MmaScrapers\Enums;

/**
 * Enum representing the source of the scraped data. The values indicate the priority of the sources, with higher values representing more reliable sources.
 * - SHERDOG: 3 (most reliable)
 * - TAPOLOGY: 2
 * - OFFICIAL: 1 (least reliable)
 */
enum Source: int
{
    case SHERDOG = 3;
    case TAPOLOGY = 2;
    case OFFICIAL = 1;
}
