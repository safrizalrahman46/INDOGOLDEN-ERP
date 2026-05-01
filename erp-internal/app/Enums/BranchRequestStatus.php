<?php

namespace App\Enums;

enum BranchRequestStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Reviewed = 'reviewed';
    case Approved = 'approved';
    case Packed = 'packed';
    case Shipped = 'shipped';
    case Received = 'received';
    case Rejected = 'rejected';
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
