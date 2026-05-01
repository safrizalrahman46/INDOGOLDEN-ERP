<?php

namespace App\Enums;

enum BranchRequestItemStatus: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Partial = 'partial';
    case Packed = 'packed';
    case Shipped = 'shipped';
    case Received = 'received';
    case OutOfStock = 'out_of_stock';
    case Substituted = 'substituted';
    case Rejected = 'rejected';

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
