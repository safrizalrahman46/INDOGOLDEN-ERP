<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\ChartWidget;

class FinanceTrendChart extends ChartWidget
{
    protected ?string $heading = 'Pemasukan vs Pengeluaran (7 Hari)';

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole([
            UserRole::Owner->value,
            UserRole::Finance->value,
            UserRole::HeadLogistics->value,
        ]);
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $branchId = ($user instanceof User && $user->hasRole(UserRole::Branch->value)) ? $user->branch_id : null;

        $labels = [];
        $incomes = [];
        $expenses = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d M');
            $incomes[] = (float) FinanceIncome::query()
                ->whereDate('transaction_date', $date)
                ->when($branchId, fn (Builder $query) => $query->where('branch_id', $branchId))
                ->sum('amount');

            $expenses[] = (float) FinanceExpense::query()
                ->whereDate('transaction_date', $date)
                ->when($branchId, fn (Builder $query) => $query->where('branch_id', $branchId))
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $incomes,
                    'borderColor' => '#1a9b53',
                    'backgroundColor' => 'rgba(26, 155, 83, 0.15)',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenses,
                    'borderColor' => '#d02626',
                    'backgroundColor' => 'rgba(208, 38, 38, 0.15)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
