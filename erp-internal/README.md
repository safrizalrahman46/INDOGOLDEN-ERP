# Indogolden ERP Internal

ERP internal berbasis Laravel + Filament untuk alur logistik, produksi, transfer cabang, gudang, dan keuangan dengan model stok ledger (bukan flat stock).

## Tech Stack

- PHP `^8.2`
- Laravel `^12`
- Filament `^5.5`
- PostgreSQL
- Spatie Laravel Permission

## Scope Modul

- Master data: cabang, gudang, supplier, unit, kategori item, stage item, item, resep produksi
- Inventory ledger: stock movement, stock balance, stock batch
- Produksi: production order (consumption + output movement)
- Transfer: antar gudang / cabang (submit -> approve -> ship -> receive)
- Finance: pemasukan, pengeluaran, kategori finance
- Audit: activity log
- Access control: role + policy per modul

## Alur Stage Stok

Stage yang disediakan:

1. `raw_dirty`
2. `raw_clean`
3. `wip`
4. `finished_goods`
5. `branch_stock`
6. `mro`
7. `analysis`

Keputusan bisnis yang sudah diterapkan:

- Premix masuk `wip`
- Bumbu jadi masuk `finished_goods`
- Cabang menerima `finished_goods` + `mro`
- Minus stock di-block keras (throw `InsufficientStockException`)
- Costing inventory pakai weighted average

## Role Default

Role yang tersedia:

- `owner`
- `finance`
- `head_logistics`
- `logistics_admin`
- `branch`

Dummy user hasil seeder (`password` untuk semua):

- `owner@erp.local`
- `finance@erp.local`
- `headlogistik@erp.local`
- `adminlogistik@erp.local`
- `cabang.jakarta@erp.local`
- `cabang.bekasi@erp.local`

## Struktur Data Utama

### Core

- `users` (+ relasi ke `branches`)
- `branches`
- `warehouses`
- `suppliers`
- `units`

### Item & Produksi

- `item_categories`
- `item_stages`
- `items`
- `production_recipes`
- `production_recipe_items`
- `production_orders`
- `production_order_inputs`
- `production_order_outputs`

### Inventory Ledger

- `stock_movements`
- `stock_movement_items`
- `stock_balances`
- `stock_batches`

### Transfer

- `transfers`
- `transfer_items`

### Finance & Audit

- `finance_categories`
- `finance_incomes`
- `finance_expenses`
- `activity_logs`

### Support Framework

- `cache`
- `jobs`
- tabel permission dari Spatie (`roles`, `permissions`, dst)

## Relasi Ringkas

- `branches` 1..* `warehouses`, `users`, `finance_incomes`, `finance_expenses`
- `items` -> `item_categories`, `units`, `item_stages`
- `stock_movements` 1..* `stock_movement_items`
- `stock_balances` unik per kombinasi item + stage + warehouse/branch + batch (via `balance_key`)
- `production_recipes` 1..* `production_recipe_items`
- `production_orders` 1..* `production_order_inputs` + 1..* `production_order_outputs`
- `transfers` 1..* `transfer_items`

## Setup Lokal

### 1) Install dependency

```bash
composer install
npm install
```

### 2) Konfigurasi environment

```bash
copy .env.example .env
php artisan key:generate
```

Edit `.env` untuk PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=erp_internal
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 3) Migrasi + seeding

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

### 4) Jalankan aplikasi

```bash
php artisan serve
npm run dev
```

Filament admin panel:

- `http://127.0.0.1:8000/admin`

### 5) Build production asset (opsional)

```bash
npm run build
```

## Resource Filament

Resource yang sudah tersedia di panel admin:

- Branches
- Warehouses
- Suppliers
- Units
- Item Categories
- Item Stages
- Items
- Production Recipes
- Stock Movements
- Production Orders
- Transfers
- Finance Incomes
- Finance Expenses
- Stock Balances
- Activity Logs
- Users

## Catatan Implementasi

- Service utama:
  - `StockBalanceService` (weighted average + saldo)
  - `StockMovementService` (draft/submit/approve/reject)
  - `ProductionService` (create + complete order)
  - `TransferService` (submit/approve/ship/receive)
  - `FinanceSummaryService`
- Policy per modul sudah diaktifkan berbasis role.
- Query list resource dan dashboard widget sudah dibatasi per-role/per-branch (terutama untuk user `branch`).
- Dashboard custom Filament tersedia dengan KPI + chart + pending approval + low stock + activity.

## Aksi Workflow di UI

Di halaman list dan edit untuk modul terkait, sudah tersedia action cepat sesuai flow:

- Stock Movement: `submit` -> `approve` / `reject`
- Transfer: `submit` -> `approve` / `reject` -> `ship` -> `receive`
- Production Order: `submit` -> `approve` -> `complete`

Action dibatasi oleh policy role + status dokumen, dan menampilkan notifikasi sukses/gagal langsung di panel.

## Validasi Cepat

```bash
php artisan about
php artisan filament:about
php artisan test
```

Jika command database gagal, cek kredensial PostgreSQL di `.env`.
