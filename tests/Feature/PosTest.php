<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Category;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class PosTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithPermission(): User
    {
        $role = Role::create([
            'name' => 'Kasir',
            'description' => 'Role kasir',
            'permissions' => [
                'pos.viewAny',
                'pos.create',
            ],
        ]);

        return User::create([
            'name' => 'Kasir',
            'email' => 'kasir@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);
    }

    private function createProduct($stock = 10): Product
    {
        $category = Category::create([
            'name' => 'Minuman',
        ]);

        return Product::create([
            'name' => 'Teh Botol',
            'description' => 'Minuman',
            'sku' => 'SKU001',
            'barcode' => '123456789',
            'price' => 5000,
            'cost_price' => 3000,
            'stock' => $stock,
            'category_id' => $category->id,
        ]);
    }

    private function createPaymentMethod(): PaymentMethod
    {
        return PaymentMethod::create([
            'name' => 'Cash',
            'is_cash' => true,
            'is_active' => true,
        ]);
    }

    public function test_pos_transaction_success()
    {
        $user = $this->createUserWithPermission();

        $product = $this->createProduct(10);

        $paymentMethod = $this->createPaymentMethod();

        $this->actingAs($user);

        $response = $this->postJson('/pos', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ]
            ],
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 20000,
        ]);

        $response->assertOk();

        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('transactions', [
            'payment_method_id' => $paymentMethod->id,
        ]);

        $this->assertDatabaseHas('transaction_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertEquals(
            8,
            $product->fresh()->stock
        );
    }

    public function test_pos_fails_when_stock_is_insufficient()
    {
        $user = $this->createUserWithPermission();

        $product = $this->createProduct(1);

        $paymentMethod = $this->createPaymentMethod();

        $this->actingAs($user);

        $response = $this->postJson('/pos', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                ]
            ],
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 50000,
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseCount('transactions', 0);

        $this->assertEquals(
            1,
            $product->fresh()->stock
        );
    }

    public function test_pos_fails_when_payment_is_insufficient()
    {
        $user = $this->createUserWithPermission();

        $product = $this->createProduct(10);

        $paymentMethod = $this->createPaymentMethod();

        $this->actingAs($user);

        $response = $this->postJson('/pos', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ]
            ],
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 1000,
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseCount('transactions', 0);

        $this->assertEquals(
            10,
            $product->fresh()->stock
        );
    }

    public function test_pos_fails_when_items_are_empty()
    {
        $user = $this->createUserWithPermission();

        $paymentMethod = $this->createPaymentMethod();

        $this->actingAs($user);

        $response = $this->postJson('/pos', [
            'items' => [],
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 10000,
        ]);

        $response->assertStatus(422);
    }
}
