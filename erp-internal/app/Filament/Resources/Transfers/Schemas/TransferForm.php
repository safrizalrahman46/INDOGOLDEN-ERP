<?php

namespace App\Filament\Resources\Transfers\Schemas;

use App\Enums\TransferStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('transfer_number')->required()->maxLength(40)->unique(ignoreRecord: true),
                DateTimePicker::make('transfer_date')->required()->default(now()),
                Select::make('status')
                    ->options(TransferStatus::options())
                    ->default(TransferStatus::Draft->value)
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Select::make('from_warehouse_id')->relationship('fromWarehouse', 'name')->searchable()->preload(),
                Select::make('to_warehouse_id')->relationship('toWarehouse', 'name')->searchable()->preload(),
                Select::make('from_branch_id')->relationship('fromBranch', 'name')->searchable()->preload(),
                Select::make('to_branch_id')->relationship('toBranch', 'name')->searchable()->preload(),
                Textarea::make('notes')->columnSpanFull(),
                Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        Select::make('item_id')->relationship('item', 'name')->searchable()->required(),
                        Select::make('unit_id')->relationship('unit', 'name')->searchable()->required(),
                        TextInput::make('requested_qty')->numeric()->required(),
                        TextInput::make('approved_qty')->numeric()->default(0),
                        TextInput::make('shipped_qty')->numeric()->default(0),
                        TextInput::make('received_qty')->numeric()->default(0),
                        TextInput::make('unit_cost')->numeric()->default(0),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
