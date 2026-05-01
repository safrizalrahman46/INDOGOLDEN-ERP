<?php

namespace App\Filament\Resources\HppCalculations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HppCalculationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('calc_number')->required()->maxLength(50)->unique(ignoreRecord: true),
                DatePicker::make('calc_date')->required()->default(now()),
                Select::make('branch_id')->relationship('branch', 'name')->searchable(),
                TextInput::make('product_name')->maxLength(150),
                Select::make('stage')->options([
                    'raw_material' => 'Raw Material',
                    'grooming' => 'Grooming',
                    'sorted_raw_material' => 'Sorted Raw Material',
                    'production' => 'Production',
                    'finish_goods' => 'Finish Goods',
                ])->required(),
                TextInput::make('total_raw_value')->numeric()->minValue(0)->prefix('Rp'),
                TextInput::make('total_clean_value')->numeric()->minValue(0)->prefix('Rp'),
                TextInput::make('total_production_cost')->numeric()->minValue(0)->prefix('Rp'),
                TextInput::make('hpp_per_unit')->numeric()->minValue(0)->prefix('Rp'),
                TextInput::make('selling_price')->numeric()->minValue(0)->prefix('Rp'),
                TextInput::make('profit')->numeric()->prefix('Rp'),
                TextInput::make('margin_percent')->numeric()->suffix('%'),
                Textarea::make('notes')->columnSpanFull(),
            ]);
    }
}
