<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Acl;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Tests\FeatureTest;

class AclServiceTest extends FeatureTest
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear any built-in permissions
        app('laravolt.acl')->clearPermissions();
    }

    public function test_initial_permissions()
    {
        $service = app('laravolt.acl');
        $this->assertEmpty($service->permissions());
    }

    public function test_register_permission_single()
    {
        $service = app('laravolt.acl');
        $service->registerPermission('create-user');
        $this->assertSame(['create-user'], $service->permissions());
    }

    public function test_register_permission_array()
    {
        $service = app('laravolt.acl');
        $service->registerPermission(['create-user', 'edit-user']);
        $this->assertSame(['create-user', 'edit-user'], $service->permissions());
    }

    public function test_sync_permission_without_refresh()
    {
        $service = app('laravolt.acl');
        $service->registerPermission(['create-user']);
        $service->syncPermission(false);

        $this->seeInDatabase('acl_permissions', ['name' => 'create-user']);
    }

    public function test_sync_permission_and_got_ordered_collection()
    {
        $service = app('laravolt.acl');
        $service->registerPermission(['delete', 'approve', 'edit']);
        $permissions = $service->syncPermission(false);

        $this->assertSame('approve', $permissions->first()['name']);
        $this->assertSame('edit', $permissions->last()['name']);
    }

    public function test_sync_permission_and_delete_missing_records()
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

    public function test_sync_permission_with_refresh()
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
