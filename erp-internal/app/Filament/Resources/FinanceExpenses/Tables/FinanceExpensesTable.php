<?php

namespace App\Filament\Resources\FinanceExpenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FinanceExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_number')->searchable()->sortable(),
                TextColumn::make('transaction_date')->dateTime()->sortable(),
                TextColumn::make('branch.name')->label('Branch')->toggleable(),
                TextColumn::make('category.name')->label('Category')->badge(),
                TextColumn::make('amount')->money('IDR'),
                TextColumn::make('payment_method')->badge(),
            ])
            ->filters([
                SelectFilter::make('branch_id')->relationship('branch', 'name')->label('Branch'),
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
