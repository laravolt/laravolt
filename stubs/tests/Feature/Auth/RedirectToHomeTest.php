<?php

declare(strict_types=1);

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;

test('it can redirected to home', function (): void {
    /** @var Authenticatable $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('auth::login.show'));

    $response->assertRedirect(AppServiceProvider::HOME);
});
