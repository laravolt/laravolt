<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Tests\TestCase;

test('redirect to login', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $response = $test->get(AppServiceProvider::HOME);
    $response->assertRedirect(route('auth::login.show'));
});

test('return nothing', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $response = $test->getJson(AppServiceProvider::HOME);
    $response->assertStatus(401);
});
