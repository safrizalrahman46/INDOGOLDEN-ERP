<?php

namespace App\Filament\Resources\StockMovements\Tables;

use App\Enums\ApprovalStatus;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\StockMovementService;
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

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('movement_number')->searchable()->sortable(),
                TextColumn::make('movement_date')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('movement_type')->badge()->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('total_cost')->money('IDR'),
                TextColumn::make('creator.name')->label('Created By')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (StockMovement $record): bool => $record->status === ApprovalStatus::Draft && Gate::allows('submit', $record))
                    ->action(function (StockMovement $record): void {
                        try {
                            app(StockMovementService::class)->submit($record);

                            Notification::make()
                                ->title('Stock movement berhasil disubmit')
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
                    ->visible(fn (StockMovement $record): bool => $record->status === ApprovalStatus::Submitted && Gate::allows('approve', $record))
                    ->action(function (StockMovement $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        try {
                            app(StockMovementService::class)->approve($record, $actor);

                            Notification::make()
                                ->title('Stock movement berhasil diapprove')
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
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (StockMovement $record): bool => $record->status === ApprovalStatus::Submitted && Gate::allows('reject', $record))
                    ->action(function (StockMovement $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        try {
                            app(StockMovementService::class)->reject($record, $actor);

                            Notification::make()
                                ->title('Stock movement berhasil ditolak')
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('Reject gagal')
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
