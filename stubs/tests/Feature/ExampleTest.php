<?php

declare(strict_types=1);

use Tests\TestCase;

test('the application returns a successful response', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $response = $test->get('/');

    $response->assertStatus(302);
});
