<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('logged_at')->dateTime('d M Y H:i:s')->sortable(),
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('module')->badge(),
                TextColumn::make('action')->badge(),
                TextColumn::make('description')->limit(50)->toggleable(),
            ])
            ->filters([
                SelectFilter::make('module')
                    ->options(fn () => \App\Models\ActivityLog::query()->distinct()->pluck('module', 'module')->all()),
            ]);
    }
}
