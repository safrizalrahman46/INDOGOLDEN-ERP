<?php

namespace App\Filament\Resources\ImportLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImportLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('module_name')->searchable(),
                TextColumn::make('file_name')->searchable()->toggleable(),
                TextColumn::make('importedBy.name')->label('Imported By')->toggleable(),
                TextColumn::make('total_rows')->numeric()->label('Rows'),
                TextColumn::make('success_rows')->numeric()->label('Success'),
                TextColumn::make('failed_rows')->numeric()->label('Failed'),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable(),
            ]);
    }
}
