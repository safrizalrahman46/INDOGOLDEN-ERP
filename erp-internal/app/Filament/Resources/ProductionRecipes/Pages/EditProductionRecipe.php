<?php

namespace App\Filament\Resources\ProductionRecipes\Pages;

use App\Filament\Resources\ProductionRecipes\ProductionRecipeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductionRecipe extends EditRecord
{
    protected static string $resource = ProductionRecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
