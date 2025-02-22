<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Acl;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Tests\FeatureTest;

class HasRoleAndPermissionTest extends FeatureTest
{
    use RefreshDatabase;

    public function test_user_to_role_relationship()
    {
        $user = $this->createUser();

        $this->assertInstanceOf(BelongsToMany::class, $user->roles());
        $this->assertEmpty($user->roles);
    }

    public function test_assign_non_existent_role()
    {
        $user = $this->createUser();
        $user->assignRole('Admin');

        $this->assertTrue($user->hasRole('Admin'));
    }

    public function test_assign_existing_role()
    {
        app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole('Admin');

        $this->assertTrue($user->hasRole('Admin'));
    }

    public function test_assign_existing_role_by_id()
    {
        app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole(1);

        $this->assertTrue($user->hasRole('Admin'));
    }

    public function test_assign_existing_role_by_model()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole($role);

        $this->assertTrue($user->hasRole('Admin'));
    }

    public function test_assign_role_by_array()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole([$role, 'Operator', 'Staff']);

        $this->assertTrue($user->hasRole(['Admin', 'Operator', 'Staff'], true));
    }

    public function test_revoke_role()
    {
        $user = $this->createUser();
        $user->assignRole('Admin')->assignRole('Operator');
        $user->revokeRole('Operator');

        $this->assertFalse($user->hasRole('Operator'));
    }

    public function test_revoke_role_by_array()
    {
        $user = $this->createUser();
        $user->assignRole('Admin')->assignRole('Operator');
        $user->revokeRole(['Admin', 'Operator']);

        $this->assertFalse($user->hasRole('Operator'));
        $this->assertFalse($user->hasRole('Admin'));
    }

    public function test_revoke_role_by_object()
    {
        $admin = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole($admin)->assignRole('Operator');
        $user->revokeRole([$admin]);

        $this->assertFalse($user->hasRole('Admin'));
    }

    public function test_revoke_role_by_id()
    {
        $user = $this->createUser();
        $user->assignRole('Admin')->assignRole('Operator');
        $user->revokeRole(1)->revokeRole(2);

        $this->assertFalse($user->hasRole('Admin'));
        $this->assertFalse($user->hasRole('Operator'));
    }

    public function test_has_role()
    {
        $user = $this->createUser();
        $user->assignRole(['Admin']);

        $this->assertTrue($user->hasRole(1));
        $this->assertFalse($user->hasRole(2));
        $this->assertTrue($user->hasRole([1, 2]));
        $this->assertFalse($user->hasRole([1, 2], true));
    }

    public function test_has_all_role()
    {
        $user = $this->createUser();
        $user->assignRole(['Admin']);
        $user->assignRole(['Operator']);

        $this->assertTrue($user->hasRole(1));
        $this->assertTrue($user->hasRole([1, 2]));
        $this->assertTrue($user->hasRole([1, 2], true));
    }

    public function test_sync_roles()
    {
        $user = $this->createUser();
        $admin = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $operator = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Operator']);
        $user->syncRoles([1, $operator, 'Staff']);

        $this->assertSame(3, $user->roles->count());
    }

    public function test_has_permission()
    {
        $user = $this->createUser();
        $admin = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $admin->addPermission('create');
        $admin->addPermission('edit');
        $user->assignRole($admin);
        $smoking = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'smoking']);

        $this->assertTrue($user->hasPermission('create'));
        $this->assertTrue($user->hasPermission(['create', 'edit'], true));
        $this->assertTrue($user->hasPermission([1, 2], true));
        $this->assertTrue($user->hasPermission([app(config('laravolt.epicentrum.models.permission'))->find(1), 2], true));
        $this->assertFalse($user->hasPermission('smoking'));
        $this->assertTrue($user->hasPermission(['smoking', 'create']));
        $this->assertFalse($user->hasPermission(['smoking', 'create'], true));
        $this->assertFalse($user->hasPermission('gambling'));
    }

    public function test_chaining()
    {
        $user = $this->createUser();
        $user->assignRole('Admin')->assignRole('Operator');

        $this->assertTrue($user->hasRole(['Admin', 'Operator'], true));
    }
}
