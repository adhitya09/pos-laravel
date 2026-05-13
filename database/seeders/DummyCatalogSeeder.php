<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\Product;

class DummyCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks so truncation won't fail
        Schema::disableForeignKeyConstraints();

        $tables = [
            'transaction_items',
            'transactions',
            // financial / report tables (if present)
            'cashbox_flows',
            'cash_flows',
            'cash_flow_sources',
            // product-dependent tables
            'inventory_items',
            // catalog
            'products',
            'categories',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();

        // Create exact categories
        $categoryNames = [
            'Jaket',
            'Celana Jean',
            'Celana Panjang',
            'Celana Pendek',
            'Kerudung',
        ];

        $categories = [];
        foreach ($categoryNames as $name) {
            $cat = Category::firstOrCreate(
                ['name' => $name],
                ['description' => 'Dummy category', 'created_at' => now(), 'updated_at' => now()]
            );
            $categories[$name] = $cat->id;
        }

        // Define product groups and items (canonical lists)
        $pullBear = [
            'brand' => 'PULL & BEAR',
            'items' => [
                'Faux Leather Black Biker Women Jacket',
                'Oversized Smiley Face Denim Women Jacket',
                'Beige Women Jacket With Full Sleeves',
                'Recycled Fabric Puffer Mustard Men Jacket',
                'Faux Suede Black Biker Women Jacket',
                'Basic Nylon Puffer Grey Women Jacket',
                'Faux Suede Grey Biker Women Jacket',
                'Short Colourful Nylon Women Jacket',
                'Basic Denim Grey Worker Women Jacket',
                'Basic Nylon Puffer Peach Red',
                "80's Style Black Denim Women Jacket",
                'Bomber Dark Khaki Men Jacket',
                'Colour Block Puffer Men Jacket',
                'Basic Fitted Denim Women Jacket',
                'Worker Women Jacket',
                'Lightweight Pale Khaki Colour Jacket',
                'Basic Nylon Puffer Navy Women Jacket',
                'Lightweight Khaki Coloured Bomber Jacket',
                'Denim Women Jacket with Fray Detail',
                'Belted Worker Women Jacket',
                'Khaki Women Jacket with Pleats',
                'Lightweight Black Coloured Bomber Jacket',
                'Anorak Pastel Pink Women Jacket',
                'Short Red Colourful Nylon Women Jacket',
                'Cropped Women Jacket with Hood',
                'Lightweight White Coloured Bomber Jacket',
            ],
            'category' => 'Jaket',
            'min' => 1300000,
            'max' => 1900000,
        ];

        $lois = [
            'brand' => 'LOIS',
            'items' => [
                'Denim Slim CFL 396 F - Biru',
                'Basic Fit Denim CS 1809 D - Biru',
                'Slim Stretch Denim SLS 390 P1 - Hitam',
                'Slim Stretch Denim SLS 388 G - Biru',
                'Skinny Stretch Jeans - Biru',
                'Regular Fit Denim - Hitam',
                'Slim Fit Denim - Dark Blue',
                'Classic Fit Denim CS 465 E - Biru',
                'Denim Straight CFS 385 E - Biru',
                'Comfort Slim Fit Denim CSL 465 E - Biru',
                'Basic Fit Denim CS 1809 C - Biru',
                'Denim Slim CFL 385 E - Biru',
                'Classic Fit Denim CS 465 C - Biru',
                'Slim Stretch Denim SLS 389 K - Biru',
                'Comfort Slim Fit Denim CSL 465 C - Biru',
                'Short Denim CFD 395 G - Biru',
            ],
            'category' => 'Celana Jean',
            'min' => 700000,
            'max' => 900000,
        ];

        $eiger = [
            'brand' => 'EIGER',
            'items' => [
                // Normalize ambiguous 'Reversible' to 'Reversible Shorts'
                'Reversible Shorts',
                'Legion Shorts',
                'Highland Pants',
                'Quester XT28 Pants',
                'Cropped Cargo Women',
                'Tactical Outdoor Pants',
                'Riding Galvanise',
                'Ascend Track 1.0',
                'C235',
                'Outcamp Short',
                '1989 Woman X-Lagos Cropped Cargo',
            ],
            'category' => null,
            'min' => 200000,
            'max' => 600000,
        ];

        $bella = [
            'brand' => 'BELLA',
            'items' => [
                'Square warna Milo',
                'Square warna Broken White',
                'Square warna Mustard',
                'Square warna Latte',
                'Square warna Beige',
                'Square warna Black',
                'Square warna Nude',
                'Square warna Olive',
            ],
            'category' => 'Kerudung',
            'fixed_price' => 15000,
        ];

        $groups = [$pullBear, $lois, $eiger, $bella];

        $skuCounter = 1;
        $barcode = 8900000000000;

        foreach ($groups as $group) {
            foreach ($group['items'] as $item) {
                $name = $group['brand'] . ' - ' . $item;
                $sku = sprintf('SKU-%04d', $skuCounter++);
                $barcode++;
                if (isset($group['fixed_price'])) {
                    $price = $group['fixed_price'];
                } else {
                    $price = rand($group['min'], $group['max']);
                }
                $costPrice = round($price * 0.6, 2);
                $stock = rand(5, 50);

                $categoryName = $group['category'];
                if ($categoryName === null) {
                    $lower = strtolower($item);
                    if (strpos($lower, 'short') !== false || strpos($lower, 'cropped') !== false) {
                        $categoryName = 'Celana Pendek';
                    } else {
                        $categoryName = 'Celana Panjang';
                    }
                }

                $categoryId = $categories[$categoryName] ?? null;
                if (! $categoryId) {
                    continue;
                }

                Product::create([
                    'name' => $name,
                    'description' => $group['brand'] . ' ' . $item,
                    'sku' => $sku,
                    'barcode' => (string) $barcode,
                    'cost_price' => $costPrice,
                    'price' => $price,
                    'stock' => $stock,
                    'image' => null,
                    'is_active' => true,
                    'category_id' => $categoryId,
                ]);
            }
        }
    }
}
