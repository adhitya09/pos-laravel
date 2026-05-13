<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RoleUserTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $role = Role::create([
            'name' => 'Super Admin',
            'permissions' => [
                'role.viewAny',
                'role.create',
                'role.update',
                'role.delete',
                'user.viewAny',
                'user.create',
                'user.update',
                'user.delete',
            ],
        ]);

        return User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);
    }

    public function test_user_can_create_role()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin);

        $response = $this->post('/role', [
            'name' => 'Kasir',
            'description' => 'Role kasir',
            'permissions' => [
                'pos.viewAny',
                'pos.create',
            ],
        ]);

        $response->assertRedirect('/role');

        $this->assertDatabaseHas('roles', [
            'name' => 'Kasir',
        ]);
    }

    public function test_role_permissions_are_saved()
    {
        $role = Role::create([
            'name' => 'Kasir',
            'permissions' => [
                'pos.viewAny',
                'pos.create',
            ],
        ]);

        $this->assertContains(
            'pos.viewAny',
            $role->permissions
        );

        $this->assertContains(
            'pos.create',
            $role->permissions
        );
    }

    public function test_user_can_update_role()
    {
        $admin = $this->createAdmin();

        $role = Role::create([
            'name' => 'Kasir',
            'permissions' => [],
        ]);

        $this->actingAs($admin);

        $response = $this->put('/role/' . $role->id, [
            'name' => 'Kasir Baru',
            'description' => 'Update role',
            'permissions' => [
                'produk.viewAny',
            ],
        ]);

        $response->assertRedirect('/role');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Kasir Baru',
        ]);
    }

    public function test_user_can_delete_role()
    {
        $admin = $this->createAdmin();

        $role = Role::create([
            'name' => 'Kasir',
            'permissions' => [],
        ]);

        $this->actingAs($admin);

        $response = $this->delete('/role/' . $role->id);

        $response->assertRedirect('/role');

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_user_can_create_user()
    {
        $admin = $this->createAdmin();

        $role = Role::create([
            'name' => 'Kasir',
            'permissions' => [],
        ]);

        $this->actingAs($admin);

        $response = $this->post('/user', [
            'name' => 'User Baru',
            'email' => 'userbaru@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id,
        ]);

        $response->assertRedirect('/user');

        $this->assertDatabaseHas('users', [
            'email' => 'userbaru@test.com',
        ]);
    }

    public function test_user_can_update_user()
    {
        $admin = $this->createAdmin();

        $role = Role::create([
            'name' => 'Kasir',
            'permissions' => [],
        ]);

        $user = User::create([
            'name' => 'User Lama',
            'email' => 'lama@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($admin);

        $response = $this->put('/user/' . $user->id, [
            'name' => 'User Baru',
            'email' => 'baru@test.com',
            'password' => '',
            'password_confirmation' => '',
            'role_id' => $role->id,
        ]);

        $response->assertRedirect('/user');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'User Baru',
            'email' => 'baru@test.com',
        ]);
    }

    public function test_user_can_verify_user()
    {
        $admin = $this->createAdmin();

        $role = Role::create([
            'name' => 'Kasir',
            'permissions' => [],
        ]);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($admin);

        $response = $this->post('/user/' . $user->id . '/verify');

        $response->assertRedirect('/user');

        $this->assertNotNull(
            $user->fresh()->email_verified_at
        );
    }

    public function test_user_can_delete_user()
    {
        $admin = $this->createAdmin();

        $role = Role::create([
            'name' => 'Kasir',
            'permissions' => [],
        ]);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($admin);

        $response = $this->delete('/user/' . $user->id);

        $response->assertRedirect('/user');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
