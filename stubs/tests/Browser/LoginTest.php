<?php

declare(strict_types=1);

it('renders the Laravolt login page', function (): void {
    $page = visit('/auth/login');

    $page
        ->assertPathIs('/auth/login')
        ->assertSee('Login')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertPresent('input[name="email"]')
        ->assertPresent('input[name="password"]')
        ->type('email', 'admin@example.test')
        ->type('password', 'secret')
        ->assertValue('email', 'admin@example.test');
});
