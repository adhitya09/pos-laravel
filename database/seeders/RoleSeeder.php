<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'description' => 'Administrator with full access', 'permissions' => ['*']],
            // Normalize to localized resource keys and standardized actions
            ['name' => 'Cashier', 'description' => 'Cashier role', 'permissions' => ['transaksi.create', 'transaksi.viewAny']],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description'], 'permissions' => $role['permissions']]
            );
        }
    }
}
