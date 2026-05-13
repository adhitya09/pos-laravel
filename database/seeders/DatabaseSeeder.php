<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed payment methods first
        $this->call(PaymentMethodSeeder::class);

        // Seed full dummy catalog (categories + products)
        $this->call(DummyCatalogSeeder::class);

        // Seed cash flow sources (truncated by DummyCatalogSeeder)
        $this->call(CashFlowSourceSeeder::class);

        // Seed roles, settings and initial inventory
        $this->call(RoleSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(InventorySeeder::class);

        // User::factory(10)->create();

        // Ensure test user exists without duplicating on re-run
        \App\Models\User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );
    }
}
