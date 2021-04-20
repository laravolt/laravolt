<?php

namespace Laravolt\Platform\Concerns;

use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait Activation
{
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = DB::transaction(
            function () use ($request) {
                $user = $this->create($request->all());

                if ($user instanceof Model) {
                    $token = $this->createToken($user);
                    $this->notifyForActivation($user, $token);
                    event(new Registered($user));
                }

                return $user;
            }
        );

        if ($user instanceof RedirectResponse) {
            return $user;
        }

        return $this->registered($request, $user) ?:
            redirect()->back()->withSuccess(trans('laravolt::auth.registration_success'));
    }

    public function activate($token)
    {
        return app('laravolt.auth.registrar')->activate($token);
    }

    protected function createToken(Model $user)
    {
        $token = md5(uniqid(rand(), true));
        DB::table('users_activation')->insert([
            'user_id'    => $user->getKey(),
            'token'      => $token,
            'created_at' => Carbon::now(),
        ]);

        return $token;
    }

    protected function notifyForActivation($user, $token)
    {
        app('laravolt.auth.registrar')->notify($user, $token);
    }
}
