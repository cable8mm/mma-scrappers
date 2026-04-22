<?php

namespace Cable8mm\MmaScrapers\Aggregators;

use Cable8mm\MmaScrapers\DTO\EventDTO;

class EventAggregator
{
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

    public function isSameEvent(EventDTO $a, EventDTO $b): bool
    {
        return $this->isSameDate($a, $b)
            && $this->isSimilarName($a->name, $b->name);
    }

    private function isSameDate(EventDTO $a, EventDTO $b): bool
    {
        if (!$a->date || !$b->date) {
            return false;
        }

        return $a->date->format('Y-m-d') === $b->date->format('Y-m-d');
    }

    private function isSimilarName(string $a, string $b): bool
    {
        $a = $this->normalize($a);
        $b = $this->normalize($b);

        // 1️⃣ 완전 일치
        if ($a === $b) {
            return true;
        }

        // 🔥 2️⃣ prefix (가장 중요)
        if (str_starts_with($a, $b) || str_starts_with($b, $a)) {
            return true;
        }

        // 3️⃣ 포함 관계
        if (str_contains($a, $b) || str_contains($b, $a)) {
            return true;
        }

        // 4️⃣ fallback
        similar_text($a, $b, $percent);

        return $percent > 60;
    }

    private function normalize(string $name): string
    {
        $name = mb_strtolower($name);
        $name = preg_replace('/[^a-z0-9가-힣]/u', '', $name);

        return $name;
    }

    private function pick(mixed $a, mixed $b): mixed
    {
        return $b ?? $a;
    }
}
