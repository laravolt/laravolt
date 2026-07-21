<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Acl;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravolt\Epicentrum\Repositories\EloquentRepository;
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
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole($role->getKey());

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
        $roleModel = app(config('laravolt.epicentrum.models.role'));
        $adminId = $roleModel->where('name', 'Admin')->value('id');
        $operatorId = $roleModel->where('name', 'Operator')->value('id');
        $user->revokeRole($adminId)->revokeRole($operatorId);

        $this->assertFalse($user->hasRole('Admin'));
        $this->assertFalse($user->hasRole('Operator'));
    }

    public function test_has_role()
    {
        $user = $this->createUser();
        $user->assignRole(['Admin']);

        $adminId = app(config('laravolt.epicentrum.models.role'))->where('name', 'Admin')->value('id');
        $missingId = (string) Str::ulid();

        $this->assertTrue($user->hasRole($adminId));
        $this->assertFalse($user->hasRole($missingId));
        $this->assertTrue($user->hasRole([$adminId, $missingId]));
        $this->assertFalse($user->hasRole([$adminId, $missingId], true));
    }

    public function test_has_all_role()
    {
        $user = $this->createUser();
        $user->assignRole(['Admin']);
        $user->assignRole(['Operator']);

        $roleModel = app(config('laravolt.epicentrum.models.role'));
        $adminId = $roleModel->where('name', 'Admin')->value('id');
        $operatorId = $roleModel->where('name', 'Operator')->value('id');

        $this->assertTrue($user->hasRole($adminId));
        $this->assertTrue($user->hasRole([$adminId, $operatorId]));
        $this->assertTrue($user->hasRole([$adminId, $operatorId], true));
    }

    public function test_sync_roles()
    {
        $user = $this->createUser();
        $admin = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $operator = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Operator']);
        $user->syncRoles([$admin->getKey(), $operator, 'Staff']);

        $this->assertSame(3, $user->roles->count());
    }

    public function test_sync_roles_invalidates_user_permission_cache_and_sessions()
    {
        $user = $this->createUser();
        $operator = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Operator']);
        $this->createSessionFor($user);

        $user->syncRoles([$operator]);

        $this->assertAccessControlInvalidatedFor($user);
    }

    public function test_repository_role_update_invalidates_user_permission_cache_and_sessions()
    {
        config(['laravolt.epicentrum.role.editable' => true]);
        $user = $this->createUser();
        $operator = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Operator']);
        $this->createSessionFor($user);

        app(EloquentRepository::class)->updateAccount($user->getKey(), ['name' => 'Fulan Updated'], [$operator->getKey()]);

        $this->assertAccessControlInvalidatedFor($user);
    }

    public function test_repository_role_update_keeps_sessions_when_roles_do_not_change()
    {
        config(['laravolt.epicentrum.role.editable' => true]);
        $user = $this->createUser();
        $operator = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Operator']);
        $user->assignRole($operator);
        $this->createSessionFor($user);

        app(EloquentRepository::class)->updateAccount($user->getKey(), ['name' => 'Fulan Updated'], [$operator->getKey()]);

        $this->assertAccessControlStillValidFor($user);
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
        $permissionModel = app(config('laravolt.epicentrum.models.permission'));
        $createId = $permissionModel->where('name', 'create')->value('id');
        $editId = $permissionModel->where('name', 'edit')->value('id');

        $this->assertTrue($user->hasPermission([$createId, $editId], true));
        $this->assertTrue($user->hasPermission([$permissionModel->find($createId), $editId], true));
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
