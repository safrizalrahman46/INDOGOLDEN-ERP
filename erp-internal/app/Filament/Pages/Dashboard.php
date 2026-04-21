<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Filament\Widgets\FinanceTrendChart;
use App\Filament\Widgets\InventoryMovementChart;
use App\Filament\Widgets\KpiOverview;
use App\Filament\Widgets\LowStockItemsTable;
use App\Filament\Widgets\PendingApprovalsOverview;
use App\Filament\Widgets\RecentActivityTable;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return [];
        }

        if ($user->hasRole(UserRole::Finance->value)) {
            return [
                KpiOverview::class,
                FinanceTrendChart::class,
            ];
        }

        if ($user->hasRole(UserRole::Branch->value)) {
            return [
                KpiOverview::class,
                InventoryMovementChart::class,
                LowStockItemsTable::class,
            ];
        }

        $widgets = [
            KpiOverview::class,
            InventoryMovementChart::class,
            LowStockItemsTable::class,
        ];

        if ($user->hasAnyRole([UserRole::Owner->value, UserRole::HeadLogistics->value])) {
            return [
                PendingApprovalsOverview::class,
                ...$widgets,
                FinanceTrendChart::class,
                RecentActivityTable::class,
            ];
        }

        return $widgets;
    }
}
