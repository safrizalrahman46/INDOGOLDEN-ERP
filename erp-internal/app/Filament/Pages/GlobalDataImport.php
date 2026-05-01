<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Imports\HeadingRowsImport;
use App\Models\User;
use App\Support\Excel\ResourceExcelManager;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class GlobalDataImport extends Page
{
    use WithFileUploads;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';

    protected static ?string $navigationLabel = 'Global Data Import';

    protected static \UnitEnum|string|null $navigationGroup = 'Operations Intelligence';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.global-data-import';

    public string $module = 'items';

    public string $mode = 'upsert';

    public ?UploadedFile $file = null;

    /** @var array<int, array<string, mixed>> */
    public array $previewRows = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole([
            UserRole::Admin->value,
            UserRole::Gudang->value,
            UserRole::Owner->value,
        ]);
    }

    /**
     * @return array<string, class-string<\Illuminate\Database\Eloquent\Model>>
     */
    public function moduleMap(): array
    {
        return [
            'branches' => \App\Models\Branch::class,
            'warehouses' => \App\Models\Warehouse::class,
            'suppliers' => \App\Models\Supplier::class,
            'units' => \App\Models\Unit::class,
            'item_categories' => \App\Models\ItemCategory::class,
            'item_stages' => \App\Models\ItemStage::class,
            'items' => \App\Models\Item::class,
            'stock_balances' => \App\Models\StockBalance::class,
            'stock_movements' => \App\Models\StockMovement::class,
            'transfers' => \App\Models\Transfer::class,
            'production_orders' => \App\Models\ProductionOrder::class,
            'finance_incomes' => \App\Models\FinanceIncome::class,
            'finance_expenses' => \App\Models\FinanceExpense::class,
            'users' => \App\Models\User::class,
            'branch_sales' => \App\Models\BranchSale::class,
            'branch_requests' => \App\Models\BranchRequest::class,
            'shipment_batches' => \App\Models\ShipmentBatch::class,
            'hpp_calculations' => \App\Models\HppCalculation::class,
        ];
    }

    public function preview(): void
    {
        if (! $this->file) {
            Notification::make()->title('Pilih file dulu')->danger()->send();

            return;
        }

        $disk = config('filament.default_filesystem_disk', 'local');
        $path = $this->file->store('imports', ['disk' => $disk]);
        $absolute = Storage::disk($disk)->path($path);

        $reader = new HeadingRowsImport();
        app('excel')->import($reader, $absolute);

        $this->previewRows = $reader->rows
            ->take(15)
            ->map(fn (Collection $row): array => $row->toArray())
            ->values()
            ->all();

        Notification::make()->title('Preview siap')->success()->send();
    }

    public function import(): void
    {
        if (! $this->file) {
            Notification::make()->title('Pilih file dulu')->danger()->send();

            return;
        }

        $modelClass = $this->moduleMap()[$this->module] ?? null;
        if (! $modelClass) {
            Notification::make()->title('Modul tidak valid')->danger()->send();

            return;
        }

        $disk = config('filament.default_filesystem_disk', 'local');
        $path = $this->file->store('imports', ['disk' => $disk]);

        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        try {
            $result = app(ResourceExcelManager::class)->importFromStoredFile(
                modelClass: $modelClass,
                storedPath: $path,
                actor: $user,
                mode: $this->mode,
            );

            Notification::make()
                ->title('Import selesai')
                ->body(sprintf('Created: %d | Updated: %d | Skipped: %d', $result['created'], $result['updated'], $result['skipped']))
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Notification::make()
                ->title('Import gagal')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
