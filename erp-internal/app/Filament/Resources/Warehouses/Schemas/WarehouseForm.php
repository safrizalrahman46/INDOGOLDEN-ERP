<?php

namespace App\Filament\Resources\Warehouses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('code')->required()->maxLength(30)->unique(ignoreRecord: true),
                TextInput::make('name')->required()->maxLength(255),
                Select::make('location_type')
                    ->options([
                        'central' => 'Central Warehouse',
                        'branch' => 'Branch Warehouse',
                        'production' => 'Production',
                    ])
                    ->required()
                    ->default('central'),
                TextInput::make('pic_name')->maxLength(255),
                TextInput::make('phone')->maxLength(30),
                Textarea::make('address')->columnSpanFull(),
                Toggle::make('is_active')->default(true),
            ]);
    }
}
