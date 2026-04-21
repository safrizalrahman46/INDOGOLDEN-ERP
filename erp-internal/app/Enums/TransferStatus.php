<?php

namespace App\Enums;

enum TransferStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Shipped = 'shipped';
    case Received = 'received';
    case Rejected = 'rejected';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::Draft->value => 'Draft',
            self::Submitted->value => 'Submitted',
            self::Approved->value => 'Approved',
            self::Shipped->value => 'Shipped',
            self::Received->value => 'Received',
            self::Rejected->value => 'Rejected',
        ];
    }
}
