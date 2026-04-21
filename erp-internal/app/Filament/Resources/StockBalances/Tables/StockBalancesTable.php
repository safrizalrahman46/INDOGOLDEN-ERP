<?php

namespace App\Filament\Resources\StockBalances\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockBalancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')->searchable()->sortable(),
                TextColumn::make('stage.name')->badge(),
                TextColumn::make('warehouse.name')->label('Warehouse')->toggleable(),
                TextColumn::make('branch.name')->label('Branch')->toggleable(),
                TextColumn::make('qty_on_hand')->numeric(decimalPlaces: 2),
                TextColumn::make('avg_cost')->money('IDR'),
                TextColumn::make('total_value')->money('IDR'),
            ])
            ->filters([
                SelectFilter::make('stage_id')->relationship('stage', 'name'),
            ]);
    }
}
