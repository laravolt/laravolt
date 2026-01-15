<?php

declare(strict_types=1);

use App\Models\User;
use Tests\TestCase;

test('it can logout', function (): void {
    /** @var TestCase */
    $test = $this;

    $test->actingAs(User::factory()->create());

    $test->post(route('auth::logout'))
        ->assertRedirect('/');
});
