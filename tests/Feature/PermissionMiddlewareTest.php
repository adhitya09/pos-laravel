<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithPermissions(array $permissions): User
    {
        $role = Role::create([
            'name' => 'Test Role',
            'description' => 'Role testing',
            'permissions' => $permissions,
        ]);

        return User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);
    }

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_permission_gets_403()
    {
        $user = $this->createUserWithPermissions([]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertForbidden();
    }

    public function test_user_with_permission_can_access_dashboard()
    {
        $user = $this->createUserWithPermissions([
            'dashboard.viewAny'
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertSuccessful();
    }

    public function test_json_request_without_permission_returns_403()
    {
        $user = $this->createUserWithPermissions([]);

        $this->actingAs($user);

        $response = $this->getJson('/dashboard');

        $response->assertForbidden();
    }
}
