<?php

namespace Cable8mm\MmaScrapers\Aggregators;

use Cable8mm\MmaScrapers\DTO\EventDTO;

/**
 * The EventAggregator is responsible for removing duplicate events.
 * Events are determined to be identical based on their name and date.
 * If the names are similar or the dates are the same, they can be considered identical events.
 * The merge function combines two EventDTOs into one, preferring non-null values.
 * This is useful when multiple sources provide slightly different information about the same event.
 *
 * Example usage:
 * $agg = new EventAggregator();
 * $event1 = new EventDTO(name: 'UFC 300', date: new DateTime('2024-07-01'));
 * $event2 = new EventDTO(name: 'UFC 300: The Return', date: new DateTime('2024-07-01'));
 * if ($agg->isSameEvent($event1, $event2)) {
 *     $merged = $agg->merge($event1, $event2);
 *     // $merged will have the name 'UFC 300: The Return' and the date '2024-07-01'
 * }
 */
class EventAggregator
{
    /**
     * Merges two EventDTOs into one, preferring non-null values.
     * Throws an exception if the events are determined to be different.
     *
     * @param EventDTO $a The first event to merge.
     * @param EventDTO $b The second event to merge.
     * @return EventDTO The merged event.
     * @throws \InvalidArgumentException If the events are determined to be different.
     */
    public function merge(EventDTO $a, EventDTO $b): EventDTO
    {
        if (!$this->isSameEvent($a, $b)) {
            throw new \InvalidArgumentException('Different events');
        }

        return new EventDTO(
            name: $this->pick($a->name, $b->name),
            location: $this->pick($a->location, $b->location),
            date: $this->pick($a->date, $b->date),
            url: $this->pick($a->url, $b->url)
        );
    }

    /**
     * Determines if two EventDTOs represent the same event based on their name and date.
     *
     * @param EventDTO $a The first event to compare.
     * @param EventDTO $b The second event to compare.
     * @return bool True if the events are considered the same, false otherwise.
     */
    public function isSameEvent(EventDTO $a, EventDTO $b): bool
    {
        return $this->isSameDate($a, $b)
            && $this->isSimilarName($a->name, $b->name);
    }

    /**
     * Determines if two EventDTOs have the same date.
     *
     * @param EventDTO $a The first event to compare.
     * @param EventDTO $b The second event to compare.
     * @return bool True if both events have the same date, false otherwise.
     */
    private function isSameDate(EventDTO $a, EventDTO $b): bool
    {
        if (!$a->date || !$b->date) {
            return false;
        }

        return $a->date->format('Y-m-d') === $b->date->format('Y-m-d');
    }

    /**
     * Determines if two event names are similar enough to be considered the same.
     *
     * @param string $a The first event name to compare.
     * @param string $b The second event name to compare.
     * @return bool True if the names are considered similar, false otherwise.
     */
    private function isSimilarName(string $a, string $b): bool
    {
        $a = $this->normalize($a);
        $b = $this->normalize($b);

        if ($a === $b) {
            return true;
        }

        if (str_starts_with($a, $b) || str_starts_with($b, $a)) {
            return true;
        }

        if (str_contains($a, $b) || str_contains($b, $a)) {
            return true;
        }

        similar_text($a, $b, $percent);

        return $percent > 60;
    }

    /**
     * Normalizes an event name by converting it to lowercase and removing non-alphanumeric characters.
     *
     * @param string $name The event name to normalize.
     * @return string The normalized event name.
     */
    private function normalize(string $name): string
    {
        $name = mb_strtolower($name);
        $name = preg_replace('/[^a-z0-9가-힣]/u', '', $name);

        return $name;
    }

    /**
     * Picks the non-null value between two options, preferring the second option if both are non-null.
     *
     * @param mixed $a The first value to compare.
     * @param mixed $b The second value to compare.
     * @return mixed The chosen value, which is the second option if it's non-null, otherwise the first option.
     */
    private function pick(mixed $a, mixed $b): mixed
    {
        return $b ?? $a;
    }
}
