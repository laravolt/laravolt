<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Laravolt\Platform\Enums\Permission;
use Laravolt\Platform\Models\User;

beforeEach(function (): void {
    $this->member = User::query()->forceCreate(['name' => 'Member', 'email' => 'member@example.test', 'password' => 'secret']);
});

it('puts the settings routes behind the manage-settings ability', function (string $name): void {
    $middleware = Route::getRoutes()->getByName($name)->gatherMiddleware();

    expect($middleware)->toContain('can:'.Permission::MANAGE_SETTINGS);
})->with(['platform::settings.edit', 'platform::settings.update']);

it('does not let a user without manage-settings open the settings page', function (): void {
    $this->actingAs($this->member)
        ->get(route('platform::settings.edit'))
        ->assertForbidden();
});

it('does not let a user without manage-settings change the settings', function (): void {
    $this->actingAs($this->member)
        ->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
        ->put(route('platform::settings.update'), ['brand_name' => 'Changed'])
        ->assertForbidden();
});
