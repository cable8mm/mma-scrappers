<?php

namespace Cable8mm\MmaScrapers\Services;

/**
 * Resolves a Sherdog fighter ID from a list of candidates based on name similarity.
 *
 * This is used to match a fighter's name to the correct Sherdog ID when multiple candidates are found in search results.
 * The resolver uses a simple string similarity measure to determine the best match, and returns the ID of the best candidate if it exceeds a certain threshold.
 *
 * Example usage:
 * $resolver = new SherdogIdResolver();
 * $searchHtml = $searchScraper->search($fighter->name);
 * $candidates = $parser($searchHtml);
 * $sherdogId = $resolver->resolve($fighter->name, $candidates);
 * $fighter->sherdogId = $sherdogId;
 */
class SherdogIdResolver
{
    public function resolve(string $name, array $candidates): ?int
    {
        $bestScore = 0;
        $bestId = null;

        foreach ($candidates as $candidate) {

            if (!$candidate || !$candidate['id']) {
                continue;
            }

            $score = $this->similarity($name, $candidate['name']);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestId = $candidate['id'];
            }
        }

        // threshold
        return $bestScore > 0.7 ? $bestId : null;
    }

    private function similarity(string $a, string $b): float
    {
        $a = $this->normalize($a);
        $b = $this->normalize($b);

        similar_text($a, $b, $percent);

        return $percent / 100;
    }

    private function normalize(string $name): string
    {
        $name = mb_strtolower($name);
        $name = preg_replace('/[^a-z0-9가-힣]/u', '', $name);

        return $name;
    }
}
