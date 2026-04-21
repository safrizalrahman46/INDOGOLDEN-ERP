<?php

namespace App\Filament\Resources\ProductionRecipes\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductionRecipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')->required()->maxLength(30)->unique(ignoreRecord: true),
                TextInput::make('name')->required()->maxLength(255),
                Select::make('output_item_id')
                    ->relationship('outputItem', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('output_unit_id')
                    ->relationship('outputUnit', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('output_qty')->required()->numeric(),
                TextInput::make('yield_percentage')->numeric()->default(100),
                Toggle::make('is_active')->default(true),
                Textarea::make('notes')->columnSpanFull(),
                Repeater::make('ingredients')
                    ->relationship('ingredients')
                    ->schema([
                        Select::make('item_id')
                            ->relationship('item', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('unit_id')
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('stage_id')
                            ->relationship('stage', 'name')
                            ->searchable(),
                        TextInput::make('qty')->numeric()->required(),
                        Toggle::make('is_optional')->default(false),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
