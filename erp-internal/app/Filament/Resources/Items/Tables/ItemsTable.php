<?php

namespace App\Filament\Resources\Items\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category')->badge(),
                TextColumn::make('defaultStage.code')->label('Stage')->badge(),
                TextColumn::make('minimum_stock')->numeric(decimalPlaces: 2),
                TextColumn::make('latest_weighted_avg_cost')->money('IDR')->label('WAC'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('item_category_id')->relationship('category', 'name')->label('Category'),
                SelectFilter::make('default_stage_id')->relationship('defaultStage', 'name')->label('Stage'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
