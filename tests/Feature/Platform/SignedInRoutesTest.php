<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('requires a signed-in user to stream media', function (): void {
    expect(Route::getRoutes()->getByName('media::stream')->gatherMiddleware())->toContain('auth');
});

it('requires a signed-in user for workflow instances and tasks', function (string $name): void {
    config([
        'laravolt.workflow.routes.middleware' => ['web', 'auth'],
        'laravolt.workflow.routes.prefix' => 'workflow',
    ]);

    require __DIR__.'/../../../packages/workflow/routes/web.php';
    Route::getRoutes()->refreshNameLookups();

    expect(Route::getRoutes()->getByName($name)->gatherMiddleware())->toContain('auth');
})->with([
    'workflow::module.instances.index',
    'workflow::module.instances.show',
    'workflow::module.instances.store',
    'workflow::module.tasks.update',
]);
