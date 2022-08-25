<?php

namespace Tests\Feature;

use Tests\TestCase;

class TrustHostsMiddlewareTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_trust_hots()
    {
        $trustedHosts = app('App\Http\Middleware\TrustHosts')->hosts();
        $this->assertNotEmpty($trustedHosts);
    }
}
