<?php

namespace App\Enums;

enum ClampingStatus: string
{
    case Active = 'active';
    case Released = 'released';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Released => 'Released',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Active => 'bg-danger',
            self::Released => 'bg-success',
        };
    }
}
