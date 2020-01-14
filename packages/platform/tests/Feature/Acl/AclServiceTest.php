<?php

declare(strict_types=1);

namespace Laravolt\Tests;

class AclServiceTest extends FeatureTest
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clear any built-in permissions
        app('laravolt.acl')->clearPermissions();
    }

    public function testInitialPermissions()
    {
        $service = app('laravolt.acl');
        $this->assertEmpty($service->permissions());
    }

    public function testRegisterPermissionSingle()
    {
        $service = app('laravolt.acl');
        $service->registerPermission('create-user');
        $this->assertSame(['create-user'], $service->permissions());
    }

    public function testRegisterPermissionArray()
    {
        $service = app('laravolt.acl');
        $service->registerPermission(['create-user', 'edit-user']);
        $this->assertSame(['create-user', 'edit-user'], $service->permissions());
    }

    public function testSyncPermissionWithoutRefresh()
    {
        $service = app('laravolt.acl');
        $service->registerPermission(['create-user']);
        $service->syncPermission(false);

        $this->seeInDatabase('acl_permissions', ['name' => 'create-user']);
    }

    public function testSyncPermissionAndGotOrderedCollection()
    {
        $service = app('laravolt.acl');
        $service->registerPermission(['delete', 'approve', 'edit']);
        $permissions = $service->syncPermission(false);

        $this->assertSame('approve', $permissions->first()['name']);
        $this->assertSame('edit', $permissions->last()['name']);
    }

    public function testSyncPermissionAndDeleteMissingRecords()
    {
        $service = app('laravolt.acl');
        $service->registerPermission(['delete', 'approve', 'edit']);
        $permissions = $service->syncPermission(false);

        // approve is missing, so it should be deleted from database
        $service->clearPermissions();
        $service->registerPermission(['delete', 'edit']);
        $permissions = $service->syncPermission(false);

        $this->assertSame('Deleted', $permissions->first()['status']);
    }

    public function testSyncPermissionWithRefresh()
    {
        $service = app('laravolt.acl');
        $service->registerPermission(['delete', 'approve', 'edit']);
        $permissions = $service->syncPermission(true);

        $service->clearPermissions();
        $service->registerPermission(['delete', 'edit']);
        $permissions = $service->syncPermission(true);

        $this->assertSame('New', $permissions->first()['status']);
    }
}
