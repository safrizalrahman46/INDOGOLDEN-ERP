<?php

namespace App\Filament\Resources\FinanceIncomes\Pages;

use App\Filament\Resources\FinanceIncomes\FinanceIncomeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinanceIncome extends EditRecord
{
    protected static string $resource = FinanceIncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
