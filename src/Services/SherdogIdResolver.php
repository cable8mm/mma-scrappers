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
    /** Resolves a Sherdog fighter ID from a list of candidates based on name similarity.
    *
    * @param string $name The name of the fighter to resolve.
    * @param array $candidates An array of candidate fighters, where each candidate is an associative array with 'id' and 'name' keys.
    * @return int|null The resolved Sherdog ID if a good match is found, or null if no suitable match is found.
    */
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

    /** Calculates the similarity between two fighter names using a normalized string similarity measure.
     *
     * @param string $a The first fighter name to compare.
     * @param string $b The second fighter name to compare.
     * @return float A similarity score between 0 and 1, where 1 means identical and 0 means completely different.
     */
    private function similarity(string $a, string $b): float
    {
        $a = $this->normalize($a);
        $b = $this->normalize($b);

        similar_text($a, $b, $percent);

        return $percent / 100;
    }

    /** Normalizes a fighter's name by converting it to lowercase and removing spaces and non-alphanumeric characters.
     *
     * @param string $name The fighter's name to normalize.
     * @return string The normalized fighter name.
     */
    private function normalize(string $name): string
    {
        $name = mb_strtolower($name);
        $name = preg_replace('/[^a-z0-9가-힣]/u', '', $name);

        return $name;
    }
}
