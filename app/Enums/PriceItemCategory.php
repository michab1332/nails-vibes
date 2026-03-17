<?php

namespace App\Enums;

enum PriceItemCategory: string
{
    case NAILS = 'Paznokcie';
    case EXTENSIONS = 'Przedłużanie';
    case CORRECTION = 'Korekta paznokci (do 4 tygodni)';
    case REFILL = 'Uzupełnienie';
    case DECORATIONS = 'Zdobienia';

    public static function labels(): array
    {
        return array_column(self::cases(), 'value');
    }
}
