<?php

declare(strict_types=1);

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

test('it can redirected to home', function (): void {
    /** @var TestCase $test */
    $test = $this;

    /** @var Authenticatable $user */
    $user = User::factory()->create();

    $test->actingAs($user);

    $response = $test->get(route('auth::login.show'));

    $response->assertRedirect(AppServiceProvider::HOME);
});
