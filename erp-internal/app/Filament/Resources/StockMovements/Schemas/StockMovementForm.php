<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use App\Enums\ApprovalStatus;
use App\Enums\MovementType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('movement_number')->required()->maxLength(40)->unique(ignoreRecord: true),
                DateTimePicker::make('movement_date')->required()->default(now()),
                Select::make('movement_type')->required()->options(MovementType::options()),
                Select::make('status')
                    ->required()
                    ->options(ApprovalStatus::options())
                    ->default(ApprovalStatus::Draft->value)
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
                        Select::make('item_id')->relationship('item', 'name')->required()->searchable(),
                        Select::make('unit_id')->relationship('unit', 'name')->required()->searchable(),
                        Select::make('direction')->required()->options([
                            'in' => 'IN',
                            'out' => 'OUT',
                        ]),
                        TextInput::make('qty')->required()->numeric(),
                        TextInput::make('unit_cost')->numeric()->default(0),
                        Select::make('from_stage_id')->relationship('fromStage', 'name')->searchable(),
                        Select::make('to_stage_id')->relationship('toStage', 'name')->searchable(),
                        Select::make('from_warehouse_id')->relationship('fromWarehouse', 'name')->searchable(),
                        Select::make('to_warehouse_id')->relationship('toWarehouse', 'name')->searchable(),
                        Select::make('from_branch_id')->relationship('fromBranch', 'name')->searchable(),
                        Select::make('to_branch_id')->relationship('toBranch', 'name')->searchable(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
