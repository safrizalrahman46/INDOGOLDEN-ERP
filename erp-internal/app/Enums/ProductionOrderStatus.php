<?php

namespace App\Enums;

enum ProductionOrderStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::Draft->value => 'Draft',
            self::Submitted->value => 'Submitted',
            self::Approved->value => 'Approved',
            self::InProgress->value => 'In Progress',
            self::Completed->value => 'Completed',
            self::Cancelled->value => 'Cancelled',
        ];
    }
}
