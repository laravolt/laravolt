<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Acl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Tests\FeatureTest;

class RoleTest extends FeatureTest
{
    use RefreshDatabase;

    public function test_role_and_permission_relationship()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);

        $this->assertInstanceOf(BelongsToMany::class, $role->permissions());
        $this->assertEmpty($role->permissions);
    }

    public function test_role_and_user_relationship()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);

        $this->assertInstanceOf(BelongsToMany::class, $role->users());
        $this->assertEmpty($role->users);
    }

    public function test_add_permission_by_key()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission->getKey());

        $this->assertTrue($role->hasPermission('create'));
    }

    public function test_add_permission_by_name()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission->name);
        $this->assertTrue($role->hasPermission('create'));
    }

    public function test_add_permission_by_object()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission);
        $this->assertTrue($role->hasPermission('create'));
    }

    public function test_remove_permission_by_key()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission);
        $role->removePermission($permission->name);

        $this->assertEmpty($role->permissions);
    }

    public function test_remove_permission_by_name()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission);
        $role->removePermission($permission->getKey());

        $this->assertEmpty($role->permissions);
    }

    public function test_remove_permission_by_object()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission);
        $role->removePermission($permission);

        $this->assertEmpty($role->permissions);
    }

    public function test_has_permission()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission);

        $this->assertTrue($role->hasPermission($permission->getKey()));
        $this->assertTrue($role->hasPermission($permission->name));
        $this->assertTrue($role->hasPermission($permission));
    }

    public function test_has_not_permission()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $create = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'create']);
        $edit = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'edit']);

        $role->addPermission($create);

        $this->assertFalse($role->hasPermission($edit->getKey()));
        $this->assertFalse($role->hasPermission($edit->name));
        $this->assertFalse($role->hasPermission($edit));
        $this->assertFalse($role->hasPermission('play'));
    }

    public function test_sync_permission()
    {
        $role = app(config('laravolt.epicentrum.models.role'))->create(['name' => 'Admin']);
        $create = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'create']);
        $edit = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'edit']);
        $delete = app(config('laravolt.epicentrum.models.permission'))->create(['name' => 'delete']);

        /*
         * Test sync permission against:
         * 1. Eloquent model
         * 2. Primary key
         * 3. Existing permission name (string)
         * 3. New permission name (string)
         * */
        $role->syncPermission([$create, $edit->getKey(), $delete->name, 'approve']);

        $this->assertCount(4, $role->permissions);
    }
}
