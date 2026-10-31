<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Acl;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravolt\Epicentrum\Http\Controllers\PermissionController;
use Laravolt\Tests\FeatureTest;

class PermissionControllerTest extends FeatureTest
{
    use RefreshDatabase;

    public function test_update_writes_only_changed_descriptions(): void
    {
        $permissionModel = config('laravolt.epicentrum.models.permission');
        $changed = (new $permissionModel)->forceFill(['name' => 'edit-user', 'description' => 'Old']);
        $changed->save();
        $same = (new $permissionModel)->forceFill(['name' => 'view-user', 'description' => 'Same']);
        $same->save();

        request()->merge(['permission' => [
            $changed->getKey() => 'New',
            $same->getKey() => 'Same',
        ]]);

        DB::enableQueryLog();
        app(PermissionController::class)->update();
        $updates = collect(DB::getQueryLog())->filter(fn (array $query): bool => str_starts_with(strtolower($query['query']), 'update'));
        DB::disableQueryLog();

        $this->assertCount(1, $updates);
        $this->assertSame('New', $changed->fresh()->description);
        $this->assertSame('Same', $same->fresh()->description);
    }
}
