<?php

namespace App\Filament\Resources\ItemCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                Select::make('category_type')
                    ->required()
                    ->options([
                        'raw_material' => 'Raw Material',
                        'wip' => 'WIP',
                        'finished_goods' => 'Finished Goods',
                        'mro' => 'MRO',
                        'analysis' => 'Analysis',
                        'other' => 'Other',
                    ]),
                Toggle::make('is_active')->default(true),
            ]);
    }
}
