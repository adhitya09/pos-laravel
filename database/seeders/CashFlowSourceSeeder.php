<?php

namespace Database\Seeders;

use App\Models\CashFlowSource;
use Illuminate\Database\Seeder;

class CashFlowSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            // Inflow sources
            ['name' => 'Penjualan', 'type' => 'in', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Restore transaksi', 'type' => 'in', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Pembayaran Piutang', 'type' => 'in', 'sort_order' => 3, 'is_active' => true],
            ['name' => 'Modal Awal', 'type' => 'in', 'sort_order' => 4, 'is_active' => true],
            ['name' => 'Modal Tambahan', 'type' => 'in', 'sort_order' => 5, 'is_active' => true],
            ['name' => 'Pemasukan Lain-lain', 'type' => 'in', 'sort_order' => 6, 'is_active' => true],

            // Outflow sources
            ['name' => 'Pembelian Inventory', 'type' => 'out', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Pengembalian Modal', 'type' => 'out', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Biaya Operasional', 'type' => 'out', 'sort_order' => 3, 'is_active' => true],
            ['name' => 'Pengeluaran Lain-lain', 'type' => 'out', 'sort_order' => 4, 'is_active' => true],
        ];

        foreach ($sources as $source) {
            CashFlowSource::firstOrCreate(
                ['name' => $source['name']],
                $source
            );
        }
    }
}
