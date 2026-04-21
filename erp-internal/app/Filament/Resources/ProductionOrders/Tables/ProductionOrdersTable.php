<?php

namespace App\Filament\Resources\ProductionOrders\Tables;

use App\Enums\ProductionOrderStatus;
use App\Models\ProductionOrder;
use App\Models\User;
use App\Services\ProductionService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Throwable;

class ProductionOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')->searchable()->sortable(),
                TextColumn::make('recipe.name')->label('Recipe')->toggleable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('outputItem.name')->label('Output Item')->searchable(),
                TextColumn::make('target_qty')->numeric(decimalPlaces: 2),
                TextColumn::make('actual_qty')->numeric(decimalPlaces: 2),
                TextColumn::make('planned_date')->date(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (ProductionOrder $record): bool => $record->status === ProductionOrderStatus::Draft && Gate::allows('submit', $record))
                    ->action(function (ProductionOrder $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        try {
                            app(ProductionService::class)->submitOrder($record, $actor);

                            Notification::make()
                                ->title('Production order berhasil disubmit')
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('Submit gagal')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ProductionOrder $record): bool => $record->status === ProductionOrderStatus::Submitted && Gate::allows('approve', $record))
                    ->action(function (ProductionOrder $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        try {
                            app(ProductionService::class)->approveOrder($record, $actor);

                            Notification::make()
                                ->title('Production order berhasil diapprove')
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('Approve gagal')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-badge')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (ProductionOrder $record): bool => in_array($record->status, [ProductionOrderStatus::Submitted, ProductionOrderStatus::Approved], true) && Gate::allows('complete', $record))
                    ->action(function (ProductionOrder $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        try {
                            app(ProductionService::class)->completeOrder($record->fresh(['inputs.item', 'outputs.item']), $actor, $record->warehouse_id);

                            Notification::make()
                                ->title('Production order berhasil diselesaikan')
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('Complete gagal')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
