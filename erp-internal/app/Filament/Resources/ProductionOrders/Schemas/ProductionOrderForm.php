<?php

namespace App\Filament\Resources\ProductionOrders\Schemas;

use App\Enums\ProductionOrderStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductionOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')->required()->maxLength(40)->unique(ignoreRecord: true),
                Select::make('production_recipe_id')->relationship('recipe', 'name')->searchable()->preload(),
                Select::make('status')
                    ->required()
                    ->options(ProductionOrderStatus::options())
                    ->default(ProductionOrderStatus::Draft->value)
                    ->disabled()
                    ->dehydrated(),
                DatePicker::make('planned_date'),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
                Select::make('output_item_id')->relationship('outputItem', 'name')->required()->searchable()->preload(),
                Select::make('output_unit_id')->relationship('outputUnit', 'name')->required()->searchable()->preload(),
                Select::make('warehouse_id')->relationship('warehouse', 'name')->searchable()->preload(),
                TextInput::make('target_qty')->required()->numeric(),
                TextInput::make('actual_qty')->numeric()->default(0),
                TextInput::make('shrinkage_qty')->numeric()->default(0),
                Textarea::make('notes')->columnSpanFull(),
                Repeater::make('inputs')
                    ->relationship('inputs')
                    ->schema([
                        Select::make('item_id')->relationship('item', 'name')->required()->searchable(),
                        Select::make('unit_id')->relationship('unit', 'name')->required()->searchable(),
                        Select::make('stage_id')->relationship('stage', 'name')->searchable(),
                        TextInput::make('planned_qty')->numeric()->required(),
                        TextInput::make('actual_qty')->numeric()->required(),
                        TextInput::make('unit_cost')->numeric()->default(0),
                    ])
                    ->columnSpanFull(),
                Repeater::make('outputs')
                    ->relationship('outputs')
                    ->schema([
                        Select::make('item_id')->relationship('item', 'name')->required()->searchable(),
                        Select::make('unit_id')->relationship('unit', 'name')->required()->searchable(),
                        Select::make('stage_id')->relationship('stage', 'name')->searchable(),
                        TextInput::make('qty')->numeric()->required(),
                        TextInput::make('unit_cost')->numeric()->default(0),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
