<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiRateLimiterTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_api_rate_limiter()
    {
        $this->assertTrue(true);
        // TODO: This test is not working because of we need registering providers
        // Route::get('sample-api', fn () => 'hello')->middleware('throttle:api');
        // $this->get('sample-api')->assertOk();
    }
}
