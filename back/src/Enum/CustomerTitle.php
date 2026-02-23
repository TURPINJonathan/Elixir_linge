<?php

namespace App\Enum;

enum CustomerTitle: string
{
    case MR = 'mr';
    case MRS = 'mrs';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MR    => 'M.',
            self::MRS   => 'Mme',
            self::OTHER => 'Autre',
        };
    }
}
