<?php

namespace App\Enums;

enum PaymentType: int
{
    case CONVENIENCE= 0; // コンビニ支払い
    case CARD = 1;      // カード支払い

    public function label(): string
    {
        return match ($this) {
            self::CONVENIENCE => 'コンビニ支払い',
            self::CARD => 'カード支払い',
        };
    }
}
