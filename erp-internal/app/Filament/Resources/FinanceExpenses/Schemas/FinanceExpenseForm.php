<?php

namespace App\Filament\Resources\FinanceExpenses\Schemas;

use App\Enums\PaymentMethod;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FinanceExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('transaction_number')->required()->maxLength(40)->unique(ignoreRecord: true),
                DateTimePicker::make('transaction_date')->required()->default(now()),
                Select::make('branch_id')->relationship('branch', 'name')->searchable()->preload(),
                Select::make('supplier_id')->relationship('supplier', 'name')->searchable()->preload(),
                Select::make('finance_category_id')
                    ->relationship('category', 'name', fn ($query) => $query->where('type', 'expense'))
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('amount')->required()->numeric(),
                Select::make('payment_method')->required()->options(PaymentMethod::options()),
                Textarea::make('notes')->columnSpanFull(),
            ]);
    }
}
