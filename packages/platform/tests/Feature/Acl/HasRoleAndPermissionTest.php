<?php

declare(strict_types=1);

namespace Laravolt\Tests;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HasRoleAndPermissionTest extends FeatureTest
{
    public function testUserToRoleRelationship()
    {
        $user = $this->createUser();

        $this->assertInstanceOf(BelongsToMany::class, $user->roles());
        $this->assertEmpty($user->roles);
    }

    public function testAssignNonExistentRole()
    {
        $user = $this->createUser();
        $user->assignRole('Admin');

        $this->assertTrue($user->hasRole('Admin'));
    }

    public function testAssignExistingRole()
    {
        app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole('Admin');

        $this->assertTrue($user->hasRole('Admin'));
    }

    public function testAssignExistingRoleById()
    {
        app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole(1);

        $this->assertTrue($user->hasRole('Admin'));
    }

    public function testAssignExistingRoleByModel()
    {
        $role = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole($role);

        $this->assertTrue($user->hasRole('Admin'));
    }

    public function testAssignRoleByArray()
    {
        $role = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole([$role, 'Operator', 'Staff']);

        $this->assertTrue($user->hasRole(['Admin', 'Operator', 'Staff'], true));
    }

    public function testRevokeRole()
    {
        $user = $this->createUser();
        $user->assignRole('Admin')->assignRole('Operator');
        $user->revokeRole('Operator');

        $this->assertFalse($user->hasRole('Operator'));
    }

    public function testRevokeRoleByArray()
    {
        $user = $this->createUser();
        $user->assignRole('Admin')->assignRole('Operator');
        $user->revokeRole(['Admin', 'Operator']);

        $this->assertFalse($user->hasRole('Operator'));
        $this->assertFalse($user->hasRole('Admin'));
    }

    public function testRevokeRoleByObject()
    {
        $admin = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $user = $this->createUser();
        $user->assignRole($admin)->assignRole('Operator');
        $user->revokeRole([$admin]);

        $this->assertFalse($user->hasRole('Admin'));
    }

    public function testRevokeRoleById()
    {
        $user = $this->createUser();
        $user->assignRole('Admin')->assignRole('Operator');
        $user->revokeRole(1)->revokeRole(2);

        $this->assertFalse($user->hasRole('Admin'));
        $this->assertFalse($user->hasRole('Operator'));
    }

    public function testHasRole()
    {
        $user = $this->createUser();
        $user->assignRole(['Admin']);

        $this->assertTrue($user->hasRole(1));
        $this->assertFalse($user->hasRole(2));
        $this->assertTrue($user->hasRole([1, 2]));
        $this->assertFalse($user->hasRole([1, 2], true));
    }

    public function testHasAllRole()
    {
        $user = $this->createUser();
        $user->assignRole(['Admin']);
        $user->assignRole(['Operator']);

        $this->assertTrue($user->hasRole(1));
        $this->assertTrue($user->hasRole([1, 2]));
        $this->assertTrue($user->hasRole([1, 2], true));
    }

    public function testSyncRoles()
    {
        $user = $this->createUser();
        $admin = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $operator = app(config('laravolt.acl.models.role'))->create(['name' => 'Operator']);
        $user->syncRoles([1, $operator, 'Staff']);

        $this->assertSame(3, $user->roles->count());
    }

    public function testHasPermission()
    {
        $user = $this->createUser();
        $admin = app(config('laravolt.acl.models.role'))->create(['name' => 'Admin']);
        $admin->addPermission('create');
        $admin->addPermission('edit');
        $user->assignRole($admin);
        $smoking = app(config('laravolt.acl.models.permission'))->create(['name' => 'smoking']);

        $this->assertTrue($user->hasPermission('create'));
        $this->assertTrue($user->hasPermission(['create', 'edit'], true));
        $this->assertTrue($user->hasPermission([1, 2], true));
        $this->assertTrue($user->hasPermission([app(config('laravolt.acl.models.permission'))->find(1), 2], true));
        $this->assertFalse($user->hasPermission('smoking'));
        $this->assertTrue($user->hasPermission(['smoking', 'create']));
        $this->assertFalse($user->hasPermission(['smoking', 'create'], true));

        $this->expectException(\InvalidArgumentException::class);
        $this->assertFalse($user->hasPermission('gambling'));
    }

    public function testChaining()
    {
        $user = $this->createUser();
        $user->assignRole('Admin')->assignRole('Operator');

        $this->assertTrue($user->hasRole(['Admin', 'Operator'], true));
    }
}
