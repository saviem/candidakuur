<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Toegestaan = 'toegestaan';
    case NietToegestaan = 'niet_toegestaan';
    case Beperkt = 'beperkt';
    case Voorwaardelijk = 'voorwaardelijk';

    public function label(): string
    {
        return match ($this) {
            self::Toegestaan => 'Toegestaan',
            self::NietToegestaan => 'Niet toegestaan',
            self::Beperkt => 'Beperkt',
            self::Voorwaardelijk => 'Voorwaardelijk',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Toegestaan => 'bg-accent-soft text-accent',
            self::NietToegestaan => 'bg-deny-soft text-deny',
            self::Beperkt => 'bg-limited-soft text-limited',
            self::Voorwaardelijk => 'bg-conditional-soft text-conditional',
        };
    }
}
