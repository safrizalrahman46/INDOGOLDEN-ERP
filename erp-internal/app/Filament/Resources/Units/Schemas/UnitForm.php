<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')->required()->maxLength(20)->unique(ignoreRecord: true),
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('precision')->required()->numeric()->default(2),
                Toggle::make('is_base')->default(false),
                Toggle::make('is_active')->default(true),
            ]);
    }
}
