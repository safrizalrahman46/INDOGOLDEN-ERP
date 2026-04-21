<?php

namespace App\Filament\Resources\ItemStages\Pages;

use App\Filament\Resources\ItemStages\ItemStageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItemStages extends ListRecords
{
    protected static string $resource = ItemStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
