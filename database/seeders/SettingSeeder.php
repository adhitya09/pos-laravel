<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['store_name' => 'Demo Store'],
            [
                'store_address' => 'Jl. Contoh No.1, Kota',
                'store_phone' => '081234567890',
                'print_type' => 'kabel',
                'printer_name' => null,
                'store_logo' => null,
            ]
        );
    }
}
