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

        $this->assertMatchesRegularExpression('/\|\s*create-user\s*\|\s*New\s*\|/', $output);
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
        $this->assertStringNotContainsString('delete-user', $output);
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

        $this->assertMatchesRegularExpression('/\|\s*edit-user\s*\|\s*Deleted\s*\|/', $output);
        $this->assertMatchesRegularExpression('/\|\s*delete-user\s*\|\s*Deleted\s*\|/', $output);
    }
}
