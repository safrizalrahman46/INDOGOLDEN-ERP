<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')->required()->maxLength(60)->unique(ignoreRecord: true),
                TextInput::make('name')->required()->maxLength(255),
                Select::make('item_category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('default_unit_id')
                    ->relationship('defaultUnit', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('default_stage_id')
                    ->relationship('defaultStage', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('item_type')
                    ->options([
                        'material' => 'Material',
                        'semi_finished' => 'Semi Finished',
                        'product' => 'Product',
                        'packaging' => 'Packaging',
                        'service' => 'Service',
                    ])
                    ->default('material')
                    ->required(),
                Toggle::make('requires_production')->default(false),
                Toggle::make('is_perishable')->default(false),
                TextInput::make('minimum_stock')->numeric()->default(0),
                TextInput::make('latest_weighted_avg_cost')->numeric()->default(0),
                Textarea::make('description')->columnSpanFull(),
                Toggle::make('is_active')->default(true),
            ]);
    }
}
