<?php

namespace App\Filament\Resources\BranchRequests\Schemas;

use App\Enums\BranchRequestItemStatus;
use App\Enums\BranchRequestStatus;
use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class BranchRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = Auth::user();
        $isBranchUser = $user instanceof User && $user->hasAnyRole([UserRole::Branch->value, UserRole::Cabang->value]);

        return $schema
            ->components([
                TextInput::make('request_number')
                    ->label('Nomor Request')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                DatePicker::make('request_date')->required()->default(now()),
                DatePicker::make('delivery_date')->required()->default(now()->addDay()),
                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->required()
                    ->default($isBranchUser ? $user?->branch_id : null)
                    ->disabled($isBranchUser)
                    ->dehydrated(),
                Select::make('status')
                    ->options(BranchRequestStatus::options())
                    ->required()
                    ->default(BranchRequestStatus::Draft->value),
                Textarea::make('note_branch')->label('Catatan Cabang')->columnSpanFull(),
                Textarea::make('note_warehouse')->label('Catatan Gudang')->columnSpanFull(),
                Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        Select::make('product_id')->relationship('product', 'name')->searchable()->required(),
                        TextInput::make('category')->maxLength(50),
                        Select::make('unit_id')->relationship('unit', 'name')->searchable(),
                        TextInput::make('requested_qty')->numeric()->required()->minValue(0),
                        TextInput::make('approved_qty')->numeric()->default(0)->minValue(0),
                        TextInput::make('packed_qty')->numeric()->default(0)->minValue(0),
                        TextInput::make('shipped_qty')->numeric()->default(0)->minValue(0),
                        TextInput::make('received_qty')->numeric()->default(0)->minValue(0),
                        TextInput::make('stock_available')->numeric()->default(0)->minValue(0),
                        Select::make('item_status')->options(BranchRequestItemStatus::options())->default(BranchRequestItemStatus::Requested->value),
                        Textarea::make('branch_note')->rows(2),
                        Textarea::make('warehouse_note')->rows(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
