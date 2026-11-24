<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Laravolt\Platform\Enums\Permission;
use Laravolt\Platform\Models\User;

it('answers the request dump only in debug mode', function (): void {
    config(['app.debug' => false]);
    $user = User::query()->forceCreate(['name' => 'Member', 'email' => 'member@example.test', 'password' => 'secret']);

    $this->actingAs($user)->get(route('platform::dump'))->assertNotFound();
});

it('puts the database backup routes behind the manage-database ability', function (string $name): void {
    // The database-monitor feature is off by default, so load its routes here.
    config(['laravolt.database-monitor.routes' => ['middleware' => ['web', 'auth'], 'prefix' => 'database-monitor']]);
    require __DIR__.'/../../../packages/database-monitor/routes/web.php';
    Route::getRoutes()->refreshNameLookups();

    $middleware = Route::getRoutes()->getByName($name)->gatherMiddleware();

    expect($middleware)->toContain('can:'.Permission::MANAGE_DB)
        ->and($middleware)->toContain('auth');
})->with(['database-monitor::backup.index', 'database-monitor::backup.store']);
