<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithDashboardPermission(): User
    {
        $role = Role::create([
            'name' => 'Admin',
            'description' => 'Role admin',
            'permissions' => ['dashboard.viewAny'],
        ]);

        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = $this->createUserWithDashboardPermission();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = $this->createUserWithDashboardPermission();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_logout()
    {
        $user = $this->createUserWithDashboardPermission();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_user_can_update_profile()
    {
        $user = $this->createUserWithDashboardPermission();

        $this->actingAs($user);

        $response = $this->put('/profile', [
            'name' => 'Nama Baru',
            'email' => 'baru@example.com',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru',
            'email' => 'baru@example.com',
        ]);
    }
}
