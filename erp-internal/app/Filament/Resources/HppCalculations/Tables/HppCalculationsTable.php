<?php

namespace App\Filament\Resources\HppCalculations\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HppCalculationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('calc_number')->searchable()->sortable(),
                TextColumn::make('calc_date')->date('d M Y')->sortable(),
                TextColumn::make('product_name')->searchable()->toggleable(),
                TextColumn::make('stage')->badge(),
                TextColumn::make('hpp_per_unit')->money('IDR')->label('HPP/Unit'),
                TextColumn::make('selling_price')->money('IDR')->label('Harga Jual'),
                TextColumn::make('margin_percent')->suffix('%')->numeric(decimalPlaces: 2),
                TextColumn::make('created_at')->dateTime('d M Y H:i')->toggleable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
