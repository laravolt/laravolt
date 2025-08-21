<?php

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('redirect to login', function () {
    $response = $this->get(AppServiceProvider::HOME);
    $response->assertRedirect(route('auth::login.show'));
});

test('return nothing', function () {
    $response = $this->getJson(AppServiceProvider::HOME);
    $response->assertStatus(401);
});
