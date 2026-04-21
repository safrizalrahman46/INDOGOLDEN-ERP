<?php

namespace App\Filament\Resources\Transfers\Tables;

use App\Enums\TransferStatus;
use App\Models\Transfer;
use App\Models\User;
use App\Services\TransferService;
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

class TransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transfer_number')->searchable()->sortable(),
                TextColumn::make('transfer_date')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('fromWarehouse.name')->label('From WH')->toggleable(),
                TextColumn::make('toWarehouse.name')->label('To WH')->toggleable(),
                TextColumn::make('fromBranch.name')->label('From Branch')->toggleable(),
                TextColumn::make('toBranch.name')->label('To Branch')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'shipped' => 'Shipped',
                        'received' => 'Received',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Transfer $record): bool => $record->status === TransferStatus::Draft && Gate::allows('submit', $record))
                    ->action(function (Transfer $record): void {
                        try {
                            app(TransferService::class)->submit($record);

                            Notification::make()
                                ->title('Transfer berhasil disubmit')
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
                    ->visible(fn (Transfer $record): bool => $record->status === TransferStatus::Submitted && Gate::allows('approve', $record))
                    ->action(function (Transfer $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        try {
                            app(TransferService::class)->approve($record, $actor);

                            Notification::make()
                                ->title('Transfer berhasil diapprove')
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
                    ->visible(fn (Transfer $record): bool => $record->status === TransferStatus::Submitted && Gate::allows('reject', $record))
                    ->action(function (Transfer $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        try {
                            app(TransferService::class)->reject($record, $actor);

                            Notification::make()
                                ->title('Transfer berhasil ditolak')
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
                Action::make('ship')
                    ->label('Ship')
                    ->icon('heroicon-o-truck')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Transfer $record): bool => $record->status === TransferStatus::Approved && Gate::allows('ship', $record))
                    ->action(function (Transfer $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        try {
                            app(TransferService::class)->ship($record, $actor);

                            Notification::make()
                                ->title('Transfer berhasil dikirim')
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('Ship gagal')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('receive')
                    ->label('Receive')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Transfer $record): bool => $record->status === TransferStatus::Shipped && Gate::allows('receive', $record))
                    ->action(function (Transfer $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        try {
                            app(TransferService::class)->receive($record, $actor);

                            Notification::make()
                                ->title('Transfer berhasil diterima')
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('Receive gagal')
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
