<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_has_default_stock_zero()
    {
        $category = Category::create([
            'name' => 'Elektronik',
        ]);

        $product = Product::create([
            'name' => 'Mouse',
            'sku' => 'SKU100',
            'price' => 50000,
            'category_id' => $category->id,
        ]);

        $this->assertEquals(
            0,
            $product->stock
        );
    }

    public function test_product_price_is_casted_to_decimal()
    {
        $category = Category::create([
            'name' => 'Minuman',
        ]);

        $product = Product::create([
            'name' => 'Kopi',
            'sku' => 'SKU101',
            'price' => 12000,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        $this->assertEquals(
            '12000.00',
            $product->price
        );
    }

    public function test_payment_method_active_status_can_be_true()
    {
        $paymentMethod = PaymentMethod::create([
            'name' => 'QRIS',
            'is_active' => true,
        ]);

        $this->assertTrue(
            $paymentMethod->is_active
        );
    }

    public function test_payment_method_active_status_can_be_false()
    {
        $paymentMethod = PaymentMethod::create([
            'name' => 'Transfer',
            'is_active' => false,
        ]);

        $this->assertFalse(
            $paymentMethod->is_active
        );
    }

    public function test_product_can_be_soft_deleted()
    {
        $category = Category::create([
            'name' => 'Snack',
        ]);

        $product = Product::create([
            'name' => 'Chiki',
            'sku' => 'SKU102',
            'price' => 3000,
            'stock' => 5,
            'category_id' => $category->id,
        ]);

        $product->delete();

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    public function test_payment_method_can_be_soft_deleted()
    {
        $paymentMethod = PaymentMethod::create([
            'name' => 'Debit',
        ]);

        $paymentMethod->delete();

        $this->assertSoftDeleted('payment_methods', [
            'id' => $paymentMethod->id,
        ]);
    }
}
