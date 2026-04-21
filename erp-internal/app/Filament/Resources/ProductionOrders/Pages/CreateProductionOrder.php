<?php

namespace App\Filament\Resources\ProductionOrders\Pages;

use App\Enums\ProductionOrderStatus;
use App\Filament\Resources\ProductionOrders\ProductionOrderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProductionOrder extends CreateRecord
{
    protected static string $resource = ProductionOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = ProductionOrderStatus::Draft;
        $data['created_by'] = Auth::id();

        return $data;
    }
}
