<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;

test('redirect to login', function (): void {
    $response = $this->get(AppServiceProvider::HOME);
    $response->assertRedirect(route('auth::login.show'));
});

test('return nothing', function (): void {
    $response = $this->getJson(AppServiceProvider::HOME);
    $response->assertStatus(401);
});
