<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Item;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LowStockItemsTable extends TableWidget
{
    protected static ?string $heading = 'Stok Menipis';

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole([
            UserRole::Owner->value,
            UserRole::HeadLogistics->value,
            UserRole::LogisticsAdmin->value,
            UserRole::Finance->value,
            UserRole::Branch->value,
        ]);
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $branchId = ($user instanceof User && $user->hasRole(UserRole::Branch->value)) ? $user->branch_id : null;

        return $table
            ->query(fn (): Builder => Item::query()
                ->withSum([
                    'stockBalances as qty_on_hand_total' => fn (Builder $query) => $query
                        ->when($branchId, fn (Builder $balanceQuery) => $balanceQuery->where('branch_id', $branchId)),
                ], 'qty_on_hand')
                ->where('minimum_stock', '>', 0)
                ->orderBy('qty_on_hand_total'))
            ->columns([
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('qty_on_hand_total')->label('Stok Tersedia')->numeric(decimalPlaces: 2),
                TextColumn::make('minimum_stock')->label('Minimum')->numeric(decimalPlaces: 2),
            ]);
    }
}
