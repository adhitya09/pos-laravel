<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class UserPermissionTest extends TestCase
{
    public function test_permission_mapping_for_index_route()
    {
        $permission = User::permissionFromRouteName('produk.index');

        $this->assertEquals('produk.viewAny', $permission);
    }

    public function test_permission_mapping_for_store_route()
    {
        $permission = User::permissionFromRouteName('produk.store', 'POST');

        $this->assertEquals('produk.create', $permission);
    }

    public function test_user_has_exact_permission()
    {
        $role = new Role([
            'permissions' => ['dashboard.viewAny']
        ]);

        $user = new User();
        $user->setRelation('role', $role);

        $this->assertTrue($user->hasPermission('dashboard.viewAny'));
    }

    public function test_user_has_wildcard_permission()
    {
        $role = new Role([
            'permissions' => ['produk.*']
        ]);

        $user = new User();
        $user->setRelation('role', $role);

        $this->assertTrue($user->hasPermission('produk.create'));
        $this->assertTrue($user->hasPermission('produk.update'));
        $this->assertTrue($user->hasPermission('produk.delete'));
    }

    public function test_user_does_not_have_invalid_permission()
    {
        $role = new Role([
            'permissions' => ['produk.*']
        ]);

        $user = new User();
        $user->setRelation('role', $role);

        $this->assertFalse($user->hasPermission('user.viewAny'));
    }

    public function test_user_without_role_has_no_permission()
    {
        $user = new User();

        $this->assertFalse($user->hasPermission('dashboard.viewAny'));
    }

    public function test_get_first_accessible_route()
    {
        $role = new Role([
            'permissions' => ['produk.viewAny']
        ]);

        $user = new User();
        $user->setRelation('role', $role);

        $this->assertEquals('produk.index', $user->getFirstAccessibleRoute());
    }
}
