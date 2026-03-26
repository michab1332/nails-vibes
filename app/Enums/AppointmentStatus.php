<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case SCHEDULED = 'Zaplanowana';
    case COMPLETED = 'Zakończona';
    case CANCELLED = 'Anulowana';
    case NO_SHOW = 'Nieobecność';

    public static function labels(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function color(): string
    {
        return match ($this) {
            self::SCHEDULED => 'default',
            self::COMPLETED => 'success',
            self::CANCELLED => 'destructive',
            self::NO_SHOW => 'secondary',
        };
    }
}
