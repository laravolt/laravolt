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
        Route::get('sample-api', fn () => "hello")->middleware('throttle:api');
        $this->get('sample-api')->assertOk();
    }
}
