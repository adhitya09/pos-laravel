<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ProdukTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        $role = Role::create([
            'name' => 'Admin',
            'permissions' => [
                'produk.viewAny',
                'produk.create',
                'produk.update',
                'produk.delete',
            ],
        ]);

        return User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);
    }

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Minuman',
        ]);
    }

    public function test_user_can_create_product()
    {
        Storage::fake('public');

        $user = $this->createUser();

        $category = $this->createCategory();

        $this->actingAs($user);

        $response = $this->post('/produk', [
            'name' => 'Teh Botol',
            'description' => 'Minuman',
            'sku' => '',
            'barcode' => '',
            'price' => 5000,
            'cost_price' => 3000,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('produk.jpg'),
        ]);

        $response->assertRedirect('/produk');

        $this->assertDatabaseHas('products', [
            'name' => 'Teh Botol',
        ]);

        $product = Product::first();

        $this->assertNotNull($product->sku);

        $this->assertNotNull($product->barcode);
    }

    public function test_product_validation_fails()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $response = $this->post('/produk', []);

        $response->assertSessionHasErrors([
            'name',
            'price',
            'stock',
            'category_id',
        ]);
    }

    public function test_user_can_update_product()
    {
        $user = $this->createUser();

        $category = $this->createCategory();

        $product = Product::create([
            'name' => 'Produk Lama',
            'description' => 'Lama',
            'sku' => 'SKU001',
            'barcode' => '123456',
            'price' => 5000,
            'cost_price' => 3000,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user);

        $response = $this->put('/produk/' . $product->id, [
            'name' => 'Produk Baru',
            'description' => 'Baru',
            'sku' => 'SKU001',
            'barcode' => '123456',
            'price' => 7000,
            'cost_price' => 4000,
            'stock' => 20,
            'category_id' => $category->id,
        ]);

        $response->assertRedirect('/produk');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produk Baru',
            'price' => 7000,
        ]);
    }

    public function test_user_can_soft_delete_product()
    {
        $user = $this->createUser();

        $category = $this->createCategory();

        $product = Product::create([
            'name' => 'Produk',
            'description' => 'Test',
            'sku' => 'SKU001',
            'barcode' => '123456',
            'price' => 5000,
            'cost_price' => 3000,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user);

        $response = $this->delete('/produk/' . $product->id);

        $response->assertRedirect('/produk');

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }
}
