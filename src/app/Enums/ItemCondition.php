<?php

namespace App\Enums;

enum ItemCondition: int
{
    case FINE = 4;
    case GOOD = 3;
    case FAIR = 2;
    case POOR = 1;

    public function label(): string
    {
        return match ($this) {
            self::FINE => '良好',
            self::GOOD => '目立った傷や汚れなし',
            self::FAIR => 'やや傷や汚れあり',
            self::POOR => '状態が悪い',
        };
    }
}
