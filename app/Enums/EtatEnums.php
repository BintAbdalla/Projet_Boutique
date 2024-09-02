<?php

namespace App\Enums;

enum EtatEnums: string
{
    case ACTIF = 'actif';
    case INACTIF = 'inactif';
    case BLOQUER = 'bloquer';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
