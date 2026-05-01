<?php

namespace App\Enums;

enum ShipmentBatchStatus: string
{
    case Draft = 'draft';
    case Packed = 'packed';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => str($status->value)->replace('_', ' ')->title()->toString()])
            ->all();
    }
}
