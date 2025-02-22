<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Acl;

use Illuminate\Support\Facades\Artisan;
use Laravolt\Tests\FeatureTest;

class SyncPermissionCommandTest extends FeatureTest
{
    public function test_sync_permission_command()
    {
        $service = app('laravolt.acl');
        $service->registerPermission('create-user');
        Artisan::call('laravolt:sync-permission');
        $output = Artisan::output();

        $this->assertStringContainsString('create-user', $output);
        $this->assertStringContainsString('1', $output);
        $this->assertStringContainsString('New', $output);
    }

    public function test_sync_permission_command_with_refresh()
    {
        $service = app('laravolt.acl');
        $service->registerPermission(['create-user', 'edit-user', 'delete-user']);
        Artisan::call('laravolt:sync-permission');

        $service->clearPermissions();
        $service->registerPermission('create-user');
        Artisan::call('laravolt:sync-permission', ['--refresh' => true]);
        $output = Artisan::output();

        $this->assertStringNotContainsString('edit-user', $output);
        $this->assertStringNotContainsString('2', $output);
        $this->assertStringNotContainsString('Deleted', $output);
    }

    public function test_sync_permission_command_with_deleted_records()
    {
        $service = app('laravolt.acl');
        $service->registerPermission(['create-user', 'edit-user', 'delete-user']);
        Artisan::call('laravolt:sync-permission');

        $service->clearPermissions();
        $service->registerPermission('create-user');
        Artisan::call('laravolt:sync-permission');
        $output = Artisan::output();

        $this->assertStringContainsString('edit-user', $output);
        $this->assertStringContainsString('2', $output);
        $this->assertStringContainsString('Deleted', $output);
    }
}
