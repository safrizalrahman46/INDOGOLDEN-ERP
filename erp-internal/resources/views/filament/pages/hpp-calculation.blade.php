<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-2xl bg-gradient-to-r from-red-700 via-red-600 to-rose-500 p-5 text-white shadow-sm">
            <h2 class="text-2xl font-black">HPP Calculation</h2>
            <p class="mt-1 text-sm text-red-100">Raw Material → Grooming → Sorted Raw Material → Produksi Bumbu → Produksi Pasta → Finish Goods</p>
        </div>

        <div class="grid gap-4 xl:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="font-bold">1) Input Data Bahan</h3>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <label class="text-sm">Nama Produk
                        <input wire:model.live="productName" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                    <label class="text-sm">Harga Beli
                        <input type="number" wire:model.live="hargaBeli" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                    <label class="text-sm">Stok Awal
                        <input type="number" step="0.001" wire:model.live="stokAwal" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                    <label class="text-sm">Stok Masuk
                        <input type="number" step="0.001" wire:model.live="stokMasuk" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                    <label class="text-sm">Stok Keluar
                        <input type="number" step="0.001" wire:model.live="stokKeluar" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                    <div class="rounded-lg bg-red-50 p-3 text-sm dark:bg-red-950/30">
                        <div class="text-red-700 dark:text-red-300">Sisa Stok Otomatis</div>
                        <div class="text-lg font-bold text-red-900 dark:text-red-100">{{ number_format((float)($calc['sisa_raw'] ?? 0), 3, ',', '.') }}</div>
                        <div class="text-[11px] text-red-700/70 dark:text-red-300/70">Formula: (Stok Awal + Stok Masuk) - Stok Keluar</div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="font-bold">2) Grooming Calculator</h3>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <label class="text-sm">Hasil Setelah Cuci/Petah
                        <input type="number" step="0.001" wire:model.live="hasilCuci" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                    <label class="text-sm">Hasil Selep (Bersih)
                        <input type="number" step="0.001" wire:model.live="hasilSelep" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-3 text-sm">
                    <div class="rounded-lg border border-red-100 bg-red-50 p-3 dark:border-red-900/40 dark:bg-red-950/20">
                        <div>Cleaning Loss</div>
                        <div class="text-xl font-bold">{{ number_format((float)($calc['clean_loss'] ?? 0), 3, ',', '.') }}</div>
                    </div>
                    <div class="rounded-lg border border-red-100 bg-red-50 p-3 dark:border-red-900/40 dark:bg-red-950/20">
                        <div>Penyusutan %</div>
                        <div class="text-xl font-bold">{{ number_format((float)($calc['clean_loss_pct'] ?? 0), 2, ',', '.') }}%</div>
                    </div>
                    <div class="rounded-lg border border-red-100 bg-red-50 p-3 dark:border-red-900/40 dark:bg-red-950/20">
                        <div>HPP Bersih / Unit</div>
                        <div class="text-xl font-bold">Rp {{ number_format((float)($calc['clean_hpp'] ?? 0), 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="font-bold">3) Production Cost Calculator</h3>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <label class="text-sm">Biaya Minyak <input type="number" wire:model.live="biayaMinyak" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" /></label>
                    <label class="text-sm">Biaya Gas <input type="number" wire:model.live="biayaGas" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" /></label>
                    <label class="text-sm">Biaya Selep <input type="number" wire:model.live="biayaSelep" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" /></label>
                    <label class="text-sm">Biaya Packaging <input type="number" wire:model.live="biayaPackaging" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" /></label>
                    <label class="text-sm">Tenaga Kerja <input type="number" wire:model.live="biayaTenagaKerja" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" /></label>
                    <label class="text-sm">Overhead <input type="number" wire:model.live="biayaOverhead" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" /></label>
                    <label class="text-sm sm:col-span-2">Hasil Produksi
                        <input type="number" step="0.001" wire:model.live="hasilProduksi" min="0.001" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                </div>
                <div class="mt-3 rounded-lg bg-gray-50 p-3 text-sm dark:bg-gray-800">
                    <div>Total Biaya Produksi: <span class="font-bold">Rp {{ number_format((float)($calc['total_production_cost'] ?? 0), 0, ',', '.') }}</span></div>
                    <div>HPP per Unit: <span class="font-bold">Rp {{ number_format((float)($calc['hpp_per_unit'] ?? 0), 2, ',', '.') }}</span></div>
                    <div class="text-[11px] text-gray-500">Formula: (Nilai Bahan + Biaya Tambahan) / Hasil Produksi</div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="font-bold">4) Finish Goods Calculator</h3>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <label class="text-sm">Stok Masuk FG
                        <input type="number" step="0.001" wire:model.live="stokMasukFg" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                    <label class="text-sm">Mutasi Keluar / Distribusi
                        <input type="number" step="0.001" wire:model.live="mutasiKeluarFg" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                    <label class="text-sm sm:col-span-2">Harga Jual
                        <input type="number" wire:model.live="hargaJual" min="0" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" />
                    </label>
                </div>
                <div class="mt-3 grid gap-2 text-sm">
                    <div class="rounded-lg border border-gray-200 p-2 dark:border-gray-700">Nilai Stok FG: <span class="font-semibold">Rp {{ number_format((float)($calc['nilai_stok_fg'] ?? 0), 0, ',', '.') }}</span></div>
                    <div class="rounded-lg border border-gray-200 p-2 dark:border-gray-700">Nilai Mutasi: <span class="font-semibold">Rp {{ number_format((float)($calc['nilai_mutasi_fg'] ?? 0), 0, ',', '.') }}</span></div>
                    <div class="rounded-lg border border-gray-200 p-2 dark:border-gray-700">Nilai Sisa: <span class="font-semibold">Rp {{ number_format((float)($calc['nilai_sisa_fg'] ?? 0), 0, ',', '.') }}</span></div>
                    <div class="rounded-lg border border-gray-200 p-2 dark:border-gray-700">Profit Estimasi: <span class="font-semibold">Rp {{ number_format((float)($calc['profit'] ?? 0), 0, ',', '.') }}</span> ({{ number_format((float)($calc['margin_pct'] ?? 0), 2, ',', '.') }}%)</div>
                    @if(($calc['warning'] ?? false) === true)
                        <div class="rounded-lg border border-amber-300 bg-amber-50 p-2 text-amber-900 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-200">Harga jual di bawah HPP.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-950/30"><div class="text-xs uppercase text-red-700 dark:text-red-300">Raw Cost</div><div class="mt-1 text-lg font-bold">Rp {{ number_format((float)($calc['nilai_total_raw'] ?? 0), 0, ',', '.') }}</div></div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-900/50 dark:bg-rose-950/30"><div class="text-xs uppercase text-rose-700 dark:text-rose-300">Cleaning Loss</div><div class="mt-1 text-lg font-bold">{{ number_format((float)($calc['clean_loss'] ?? 0), 3, ',', '.') }}</div></div>
            <div class="rounded-xl border border-orange-200 bg-orange-50 p-4 dark:border-orange-900/50 dark:bg-orange-950/30"><div class="text-xs uppercase text-orange-700 dark:text-orange-300">Production Cost</div><div class="mt-1 text-lg font-bold">Rp {{ number_format((float)($calc['total_production_cost'] ?? 0), 0, ',', '.') }}</div></div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/30"><div class="text-xs uppercase text-emerald-700 dark:text-emerald-300">Final HPP</div><div class="mt-1 text-lg font-bold">Rp {{ number_format((float)($calc['hpp_per_unit'] ?? 0), 2, ',', '.') }}</div></div>
        </div>
    </div>
</x-filament-panels::page>
