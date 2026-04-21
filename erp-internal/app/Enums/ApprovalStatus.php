<?php

namespace App\Enums;

enum ApprovalStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
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
            self::Rejected->value => 'Rejected',
        ];
    }
}
