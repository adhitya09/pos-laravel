<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_products_relation()
    {
        $category = Category::create([
            'name' => 'Makanan',
        ]);

        $product = Product::create([
            'name' => 'Indomie',
            'sku' => 'SKU001',
            'price' => 5000,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        $this->assertTrue(
            $category->products->contains($product)
        );
    }

    public function test_transaction_has_transaction_items_relation()
    {
        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
        ]);

        $transaction = Transaction::create([
            'invoice_no' => 'INV001',
            'customer_name' => 'Customer',
            'total_amount' => 10000,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'payment_method_id' => $paymentMethod->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        $category = Category::create([
            'name' => 'Snack',
        ]);

        $product = Product::create([
            'name' => 'Chitato',
            'sku' => 'SKU002',
            'price' => 10000,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        $item = TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 10000,
            'subtotal' => 10000,
        ]);

        $this->assertTrue(
            $transaction->transactionItems->contains($item)
        );
    }

    public function test_transaction_belongs_to_payment_method()
    {
        $paymentMethod = PaymentMethod::create([
            'name' => 'QRIS',
        ]);

        $transaction = Transaction::create([
            'invoice_no' => 'INV002',
            'customer_name' => 'Customer',
            'total_amount' => 5000,
            'paid_amount' => 5000,
            'change_amount' => 0,
            'payment_method_id' => $paymentMethod->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        $this->assertEquals(
            'QRIS',
            $transaction->paymentMethod->name
        );
    }

    public function test_transaction_item_belongs_to_product()
    {
        $category = Category::create([
            'name' => 'Minuman',
        ]);

        $product = Product::create([
            'name' => 'Teh Botol',
            'sku' => 'SKU003',
            'price' => 4000,
            'stock' => 20,
            'category_id' => $category->id,
        ]);

        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
        ]);

        $transaction = Transaction::create([
            'invoice_no' => 'INV003',
            'customer_name' => 'Customer',
            'total_amount' => 4000,
            'paid_amount' => 5000,
            'change_amount' => 1000,
            'payment_method_id' => $paymentMethod->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        $item = TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 4000,
            'subtotal' => 4000,
        ]);

        $this->assertEquals(
            'Teh Botol',
            $item->product->name
        );
    }

    public function test_payment_method_has_transactions_relation()
    {
        $paymentMethod = PaymentMethod::create([
            'name' => 'Transfer',
        ]);

        Transaction::create([
            'invoice_no' => 'INV004',
            'customer_name' => 'Customer',
            'total_amount' => 15000,
            'paid_amount' => 15000,
            'change_amount' => 0,
            'payment_method_id' => $paymentMethod->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        $this->assertCount(
            1,
            $paymentMethod->transactions
        );
    }
}
