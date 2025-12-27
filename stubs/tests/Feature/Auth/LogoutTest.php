<?php

declare(strict_types=1);

use App\Models\User;

test('it can logout', function (): void {
    $this->actingAs(User::factory()->create());

    $this->post(route('auth::logout'))
        ->assertRedirect('/');
});
