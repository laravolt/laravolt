<?php

namespace Laravolt\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Laravolt\Platform\Models\User;

class ActivationTest extends FeatureTest
{
    /**
     * @test
     */
    public function it_can_activate_user_via_activation_link()
    {
        $status = 'ACTIVE';
        $this->app['config']->set('laravolt.auth.activation.status_after', $status);

        $token = 'asdf1234';
        $user = User::create(['name' => 'Budi', 'email' => 'budi@laravolt.app', 'password' => bcrypt('asdf1234'), 'status' => 'PENDING']);
        DB::table('users_activation')->insert(['user_id' => $user->getKey(), 'token' => $token, 'created_at' => Carbon::now()]);

        $this->visitRoute('auth::activate', $token)
             ->seeRouteIs('auth::login');

        $this->seeInDatabase((new User())->getTable(), ['id' => $user->getKey(), 'status' => $status]);
    }

    /**
     * @test
     */
    public function it_can_handle_bad_activation_link()
    {
        $statusBefore = 'PENDING';
        $this->app['config']->set('laravolt.auth.activation.status_before', $statusBefore);

        $token = 'asdf1234';
        $user = User::create(['name' => 'Citra', 'email' => 'citra@laravolt.app', 'password' => bcrypt('asdf1234'), 'status' => $statusBefore]);
        DB::table('users_activation')->insert(['user_id' => $user->getKey(), 'token' => $token, 'created_at' => Carbon::now()]);

        $this->route('GET', 'auth::activate', 'badtoken1234');
        $this->assertResponseStatus(404);

        $this->seeInDatabase((new User())->getTable(), ['id' => $user->getKey(), 'status' => $statusBefore]);
    }
}
