<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\StockMovementItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\ChartWidget;

class InventoryMovementChart extends ChartWidget
{
    protected ?string $heading = 'Barang Masuk vs Keluar (7 Hari)';

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole([
            UserRole::Owner->value,
            UserRole::HeadLogistics->value,
            UserRole::LogisticsAdmin->value,
            UserRole::Branch->value,
        ]);
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $branchId = ($user instanceof User && $user->hasRole(UserRole::Branch->value)) ? $user->branch_id : null;

        $labels = [];
        $in = [];
        $out = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d M');
            $in[] = (float) StockMovementItem::query()
                ->whereDate('created_at', $date)
                ->when($branchId, fn (Builder $query) => $query->where(function (Builder $movementQuery) use ($branchId) {
                    $movementQuery
                        ->where('from_branch_id', $branchId)
                        ->orWhere('to_branch_id', $branchId);
                }))
                ->where('direction', 'in')
                ->sum('qty');

            $out[] = (float) StockMovementItem::query()
                ->whereDate('created_at', $date)
                ->when($branchId, fn (Builder $query) => $query->where(function (Builder $movementQuery) use ($branchId) {
                    $movementQuery
                        ->where('from_branch_id', $branchId)
                        ->orWhere('to_branch_id', $branchId);
                }))
                ->where('direction', 'out')
                ->sum('qty');
        }

        return [
            'datasets' => [
                [
                    'label' => 'IN',
                    'data' => $in,
                    'backgroundColor' => '#f03d3d',
                ],
                [
                    'label' => 'OUT',
                    'data' => $out,
                    'backgroundColor' => '#1f2937',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
