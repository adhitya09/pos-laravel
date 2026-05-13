<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\PaymentMethod;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        $role = Role::create([
            'name' => 'Admin',
            'permissions' => [
            'payment-method.viewAny',
            'payment-method.create',
            'payment-method.update',
            'payment-method.delete',
            'payment-method.restore',
        ],
        ]);

        return User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);
    }

    public function test_user_can_create_payment_method()
    {
        Storage::fake('public');

        $user = $this->createUser();

        $this->actingAs($user);

        $response = $this->post('/payment-method', [
            'name' => 'QRIS',
            'description' => 'Pembayaran QRIS',
            'is_cash' => false,
            'is_active' => true,
            'logo' => UploadedFile::fake()->image('logo.png'),
        ]);

        $response->assertRedirect('/payment-method');

        $this->assertDatabaseHas('payment_methods', [
            'name' => 'QRIS',
        ]);

        $paymentMethod = PaymentMethod::first();

        $this->assertNotNull($paymentMethod->logo);
    }

    public function test_payment_method_validation_fails()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $response = $this->post('/payment-method', []);

        $response->assertSessionHasErrors([
            'name',
        ]);
    }

    public function test_user_can_update_payment_method()
    {
        $user = $this->createUser();

        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Tunai',
            'is_cash' => true,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->put('/payment-method/' . $paymentMethod->id, [
            'name' => 'Cash Update',
            'description' => 'Tunai Baru',
            'is_cash' => true,
            'is_active' => true,
        ]);

        $response->assertRedirect('/payment-method');

        $this->assertDatabaseHas('payment_methods', [
            'id' => $paymentMethod->id,
            'name' => 'Cash Update',
        ]);
    }

    public function test_user_can_soft_delete_payment_method()
    {
        $user = $this->createUser();

        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Tunai',
            'is_cash' => true,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->delete('/payment-method/' . $paymentMethod->id);

        $response->assertRedirect('/payment-method');

        $this->assertSoftDeleted('payment_methods', [
            'id' => $paymentMethod->id,
        ]);
    }

    public function test_user_can_restore_payment_method()
    {
        $user = $this->createUser();

        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Tunai',
            'is_cash' => true,
            'is_active' => true,
        ]);

        $paymentMethod->delete();

        $this->actingAs($user);

        $response = $this->post('/payment-method/' . $paymentMethod->id . '/restore');

        $response->assertRedirect('/payment-method');

        $this->assertDatabaseHas('payment_methods', [
            'id' => $paymentMethod->id,
            'deleted_at' => null,
        ]);
    }

    public function test_is_cash_payment_method_logic()
    {
        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'is_cash' => true,
            'is_active' => true,
        ]);

        $this->assertTrue(
            $paymentMethod->isCashPayment()
        );
    }
}
