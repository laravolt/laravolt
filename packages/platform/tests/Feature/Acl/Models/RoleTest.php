<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Acl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravolt\Tests\FeatureTest;

class RoleTest extends FeatureTest
{
    public function testRoleAndPermissionRelationship()
    {
        $role = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);

        $this->assertInstanceOf(BelongsToMany::class, $role->permissions());
        $this->assertEmpty($role->permissions);
    }

    public function testRoleAndUserRelationship()
    {
        $role = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);

        $this->assertInstanceOf(BelongsToMany::class, $role->users());
        $this->assertEmpty($role->users);
    }

    public function testAddPermissionByKey()
    {
        $role = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.acl.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission->getKey());

        $this->assertTrue($role->hasPermission('create'));
    }

    public function testAddPermissionByName()
    {
        $role = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.acl.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission->name);
        $this->assertTrue($role->hasPermission('create'));
    }

    public function testAddPermissionByObject()
    {
        $role = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.acl.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission);
        $this->assertTrue($role->hasPermission('create'));
    }

    public function testRemovePermissionByKey()
    {
        $role = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.acl.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission);
        $role->removePermission($permission->name);

        $this->assertEmpty($role->permissions);
    }

    public function testRemovePermissionByName()
    {
        $role = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.acl.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission);
        $role->removePermission($permission->getKey());

        $this->assertEmpty($role->permissions);
    }

    public function testRemovePermissionByObject()
    {
        $role = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $permission = app(config('laravolt.acl.models.permission'))->create(['name' => 'create']);

        $role->addPermission($permission);
        $role->removePermission($permission);

        $this->assertEmpty($role->permissions);
    }
}
