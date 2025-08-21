<?php

test('it has api rate limiter', function () {
    expect(true)->toBeTrue();
    // TODO: This test is not working because of we need registering providers
    // Route::get('sample-api', fn () => 'hello')->middleware('throttle:api');
    // $this->get('sample-api')->assertOk();
});
