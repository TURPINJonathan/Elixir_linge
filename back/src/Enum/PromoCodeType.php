<?php

namespace App\Enum;

enum PromoCodeType: string
{
    case CUSTOM = 'custom';
    case GENERAL = 'general';
    case SERVICE = 'service';

    public function label(): string
    {
        return match ($this) {
            self::CUSTOM  => 'Personnalisé',
            self::GENERAL => 'Général',
            self::SERVICE => 'Service',
        };
    }
}
