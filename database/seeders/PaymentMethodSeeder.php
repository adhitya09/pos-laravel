<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Tunai',
                'description' => 'Pembayaran dengan uang tunai',
                'is_cash' => true,
                'is_active' => true,
            ],
            [
                'name' => 'QRIS',
                'description' => 'Pembayaran dengan QRIS (Quick Response Code Indonesian Standard)',
                'is_cash' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Transfer Bank',
                'description' => 'Pembayaran melalui transfer bank',
                'is_cash' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Kartu Kredit',
                'description' => 'Pembayaran dengan kartu kredit',
                'is_cash' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Debit Card',
                'description' => 'Pembayaran dengan kartu debit',
                'is_cash' => false,
                'is_active' => true,
            ],
            [
                'name' => 'E-Wallet',
                'description' => 'Pembayaran dengan e-wallet (OVO, GoPay, DANA, dll)',
                'is_cash' => false,
                'is_active' => true,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(['name' => $method['name']], $method);
        }
    }
}
