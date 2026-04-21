<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Qris = 'qris';
    case Other = 'other';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::Cash->value => 'Cash',
            self::BankTransfer->value => 'Bank Transfer',
            self::Qris->value => 'QRIS',
            self::Other->value => 'Other',
        ];
    }
}
