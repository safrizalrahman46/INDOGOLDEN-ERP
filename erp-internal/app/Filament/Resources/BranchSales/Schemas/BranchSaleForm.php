<?php

namespace App\Filament\Resources\BranchSales\Schemas;

use App\Enums\BranchSaleStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BranchSaleForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = Auth::user();
        $isBranchUser = $user instanceof User && $user->isBranchLike();

        return $schema
            ->components([
                TextInput::make('sale_number')
                    ->required()
                    ->maxLength(40)
                    ->default(fn () => 'NOTA-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)))
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('sale_date')
                    ->required()
                    ->default(now()),
                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default($isBranchUser ? $user?->branch_id : null)
                    ->disabled($isBranchUser)
                    ->dehydrated(),
                Select::make('status')
                    ->options(BranchSaleStatus::options())
                    ->default(BranchSaleStatus::Draft->value)
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Select::make('payment_method')
                    ->options(PaymentMethod::options())
                    ->default(PaymentMethod::Cash->value)
                    ->required(),
                TextInput::make('subtotal')->numeric()->default(0)->disabled()->dehydrated(),
                TextInput::make('discount_amount')->numeric()->default(0),
                TextInput::make('tax_amount')->numeric()->default(0),
                TextInput::make('total_amount')->numeric()->default(0)->disabled()->dehydrated(),
                TextInput::make('cogs_amount')->numeric()->default(0)->disabled()->dehydrated(),
                TextInput::make('gross_profit')->numeric()->default(0)->disabled()->dehydrated(),
                Textarea::make('notes')->columnSpanFull(),
                Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        Select::make('item_id')
                            ->relationship('item', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('unit_id')
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->required(),
                        TextInput::make('qty')
                            ->numeric()
                            ->required(),
                        TextInput::make('unit_price')
                            ->numeric()
                            ->required(),
                        TextInput::make('line_total')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('cogs_unit')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('cogs_total')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        Textarea::make('notes'),
                    ])
                    ->columnSpanFull()
                    ->defaultItems(1),
            ]);
    }
}
