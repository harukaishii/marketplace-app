<?php

namespace App\Enums;

enum ItemStatus: int
{
    case AVAILABLE = 0; // 販売中
    case SOLD = 1;      // 売却済み

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => '販売中',
            self::SOLD => '売却済み',
        };
    }
}
