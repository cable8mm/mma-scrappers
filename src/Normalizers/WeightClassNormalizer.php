<?php

namespace Cable8mm\MmaScrapers\Normalizers;

use Cable8mm\MmaScrapers\Enums\WeightClass;

class WeightClassNormalizer
{
    public static function normalize(string $value): ?WeightClass
    {
        $value = preg_replace('/_/', ' ', strtolower(trim($value)));

        return match (true) {
            str_contains($value, 'straw') => WeightClass::STRAWWEIGHT,
            str_contains($value, 'fly') => WeightClass::FLYWEIGHT,
            str_contains($value, 'bantam') => WeightClass::BANTAMWEIGHT,
            str_contains($value, 'feather') => WeightClass::FEATHERWEIGHT,
            str_contains($value, 'lightweight') => WeightClass::LIGHTWEIGHT,
            str_contains($value, 'welter') => WeightClass::WELTERWEIGHT,
            str_contains($value, 'middle') => WeightClass::MIDDLEWEIGHT,
            str_contains($value, 'light heavy') => WeightClass::LIGHT_HEAVYWEIGHT,
            str_contains($value, 'heavy') => WeightClass::HEAVYWEIGHT,
            str_contains($value, 'catch') => WeightClass::CATCHWEIGHT,
            str_contains($value, 'open') => WeightClass::OPENWEIGHT,
            default => null,
        };
    }
}
