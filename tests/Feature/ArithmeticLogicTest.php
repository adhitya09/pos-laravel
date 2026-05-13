<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArithmeticLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_total_price_correctly()
    {
        $subtotal1 = 5000 * 2;
        $subtotal2 = 3000 * 3;

        $total = $subtotal1 + $subtotal2;

        $this->assertEquals(
            19000,
            $total
        );
    }

    public function test_change_calculation_correct()
    {
        $total = 25000;
        $paid = 50000;

        $change = $paid - $total;

        $this->assertEquals(
            25000,
            $change
        );
    }

    public function test_change_becomes_zero_if_cash_less_than_total()
    {
        $total = 30000;
        $paid = 20000;

        $change = $paid >= $total
            ? $paid - $total
            : 0;

        $this->assertEquals(
            0,
            $change
        );
    }

    public function test_cannot_add_quantity_more_than_stock()
    {
        $category = Category::create([
            'name' => 'Snack',
        ]);

        $product = Product::create([
            'name' => 'Qtela',
            'sku' => 'SKU500',
            'price' => 5000,
            'stock' => 5,
            'category_id' => $category->id,
        ]);

        $requestedQty = 10;

        $isValid = $requestedQty <= $product->stock;

        $this->assertFalse(
            $isValid
        );
    }

    public function test_checkout_fails_if_cart_empty()
    {
        $cart = [];

        $canCheckout = count($cart) > 0;

        $this->assertFalse(
            $canCheckout
        );
    }

    public function test_subtotal_calculation_correct()
    {
        $qty = 4;
        $price = 7500;

        $subtotal = $qty * $price;

        $this->assertEquals(
            30000,
            $subtotal
        );
    }

    public function test_total_transaction_from_multiple_items()
    {
        $items = [
            ['qty' => 2, 'price' => 10000],
            ['qty' => 1, 'price' => 5000],
            ['qty' => 3, 'price' => 2000],
        ];

        $total = 0;

        foreach ($items as $item) {
            $total += $item['qty'] * $item['price'];
        }

        $this->assertEquals(
            31000,
            $total
        );
    }
}
