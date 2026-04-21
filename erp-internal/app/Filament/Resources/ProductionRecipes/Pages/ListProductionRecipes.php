<?php

namespace App\Filament\Resources\ProductionRecipes\Pages;

use App\Filament\Resources\ProductionRecipes\ProductionRecipeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductionRecipes extends ListRecords
{
    protected static string $resource = ProductionRecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
