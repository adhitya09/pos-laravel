<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\PaymentMethod;
use App\Models\CashboxFlow;
use App\Models\CashFlowSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;


class TransaksiTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        $role = Role::create([
            'name' => 'Admin',
            'permissions' => [
                'transaksi.viewAny',
                'transaksi.view',
                'transaksi.delete',
            ],
        ]);

        return User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);
    }

    private function createProduct($stock = 10): Product
    {
        $category = Category::create([
            'name' => 'Makanan',
        ]);

        return Product::create([
            'name' => 'Roti',
            'description' => 'Roti Enak',
            'sku' => 'SKU001',
            'barcode' => '123456789',
            'price' => 5000,
            'cost_price' => 3000,
            'stock' => $stock,
            'category_id' => $category->id,
        ]);
    }

    private function createCashFlowSource(): CashFlowSource
{
    return CashFlowSource::create([
        'name' => 'Penjualan POS',
        'type' => 'in',
        'description' => 'Sumber pemasukan POS',
    ]);
}

    private function createTransaction(Product $product): Transaction
    {
        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'is_cash' => true,
            'is_active' => true,
        ]);

        $transaction = Transaction::create([
            'invoice_no' => 'INV001',
            'customer_name' => 'Customer',
            'total_amount' => 10000,
            'paid_amount' => 15000,
            'change_amount' => 5000,
            'payment_method_id' => $paymentMethod->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 5000,
            'subtotal' => 10000,
        ]);

        $product->decrement('stock', 2);

        $source = $this->createCashFlowSource();

        CashboxFlow::create([
            'type' => 'in',
            'source_id' => $source->id,
            'amount' => 10000,
            'date' => now(),
            'notes' => 'Auto POS',
            'reference_type' => 'transaction',
            'reference_id' => $transaction->id,
            'is_auto' => true,
        ]);

        return $transaction;
    }

    public function test_user_can_view_transaction_index()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $response = $this->get('/transaksi');

        $response->assertSuccessful();
    }

    public function test_user_can_view_transaction_detail()
    {
        $user = $this->createUser();

        $product = $this->createProduct();

        $transaction = $this->createTransaction($product);

        $this->actingAs($user);

        $response = $this->get('/transaksi/' . $transaction->id);

        $response->assertSuccessful();
    }

    public function test_destroy_transaction_restores_stock_and_deletes_cashflow()
    {
        $user = $this->createUser();

        $product = $this->createProduct(10);

        $transaction = $this->createTransaction($product);

        $this->actingAs($user);

        $response = $this->delete('/transaksi/' . $transaction->id);

        $response->assertRedirect('/transaksi');

        $this->assertDatabaseMissing('transactions', [
    'id' => $transaction->id,
        ]);

        $this->assertSoftDeleted('cashbox_flows', [
            'reference_id' => $transaction->id,
            'reference_type' => 'transaction',
        ]);

        $this->assertEquals(
            10,
            $product->fresh()->stock
        );
    }
}
