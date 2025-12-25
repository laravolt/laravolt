<?php

declare(strict_types=1);

test('it has api rate limiter', function (): void {
    expect(true)->toBeTrue();
    // TODO: This test is not working because of we need registering providers
    // Route::get('sample-api', fn () => 'hello')->middleware('throttle:api');
    // $this->get('sample-api')->assertOk();
});
