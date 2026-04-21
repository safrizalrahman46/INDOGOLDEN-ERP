<?php

namespace App\Enums;

enum UserRole: string
{
    case Owner = 'owner';
    case Finance = 'finance';
    case HeadLogistics = 'head_logistics';
    case LogisticsAdmin = 'logistics_admin';
    case Branch = 'branch';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
