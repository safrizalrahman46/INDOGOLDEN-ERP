<?php

namespace App\Filament\Resources\BranchSales\Tables;

use App\Enums\BranchSaleStatus;
use App\Models\BranchSale;
use App\Models\User;
use App\Services\BranchSaleService;
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

class BranchSalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sale_number')->searchable()->sortable(),
                TextColumn::make('sale_date')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('branch.name')->label('Branch')->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('payment_method')->badge(),
                TextColumn::make('total_amount')->money('IDR'),
                TextColumn::make('gross_profit')->money('IDR'),
                TextColumn::make('creator.name')->label('Created By')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(BranchSaleStatus::options()),
                SelectFilter::make('branch_id')->relationship('branch', 'name')->label('Branch'),
            ])
            ->recordActions([
                Action::make('post')
                    ->label('Post Nota')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (BranchSale $record): bool => $record->status === BranchSaleStatus::Draft && Gate::allows('post', $record))
                    ->action(function (BranchSale $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        try {
                            app(BranchSaleService::class)->post($record, $actor);

                            Notification::make()
                                ->title('Nota berhasil diposting')
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('Post nota gagal')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('print_thermal')
                    ->label('Print Thermal')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (BranchSale $record): string => route('branch-sales.print.thermal', ['branchSale' => $record]))
                    ->openUrlInNewTab(),
                Action::make('print_a4')
                    ->label('Print A4')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->url(fn (BranchSale $record): string => route('branch-sales.print.a4', ['branchSale' => $record]))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
