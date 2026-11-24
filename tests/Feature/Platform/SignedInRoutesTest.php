<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('requires a signed-in user to stream media', function (): void {
    expect(Route::getRoutes()->getByName('media::stream')->gatherMiddleware())->toContain('auth');
});

it('requires a signed-in user for workflow instances', function (string $name): void {
    config([
        'laravolt.workflow.routes.middleware' => ['web', 'auth'],
        'laravolt.workflow.routes.prefix' => 'workflow',
    ]);

    require __DIR__.'/../../../packages/workflow/routes/web.php';
    Route::getRoutes()->refreshNameLookups();

    $route = Route::getRoutes()->getByName($name);

    // withoutMiddleware() keeps 'auth' in the gathered list and records it as excluded.
    expect($route->gatherMiddleware())->toContain('auth')
        ->and($route->excludedMiddleware())->not->toContain('auth');
})->with([
    'workflow::module.instances.index',
    'workflow::module.instances.show',
    'workflow::module.instances.store',
    'workflow::definitions.xml',
]);

it('serves the public tracker diagram per tracking code, without sign-in', function (): void {
    config([
        'laravolt.workflow.routes.middleware' => ['web', 'auth'],
        'laravolt.workflow.routes.prefix' => 'workflow',
    ]);

    require __DIR__.'/../../../packages/workflow/routes/web.php';
    Route::getRoutes()->refreshNameLookups();

    $route = Route::getRoutes()->getByName('workflow::tracker.diagram');

    expect($route->uri())->toBe('workflow/{module}/tracker/{tracker}/diagram')
        ->and($route->excludedMiddleware())->toContain('auth')
        ->and($route->getActionMethod())->toBe('diagram');
});
