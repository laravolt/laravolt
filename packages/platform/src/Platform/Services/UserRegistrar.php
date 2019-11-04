<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravolt\Contracts\ShouldActivate;
use Laravolt\Platform\Mail\AccountActivationMail;

class UserRegistrar implements \Laravolt\Contracts\UserRegistrar, ShouldActivate
{
    public function validate(array $data)
    {
        return Validator::make(
            $data,
            [
                'name'     => 'required|max:255',
                'email'    => 'required|email|max:255|unique:users',
                'password' => 'required|min:6|confirmed',
            ]
        );
    }

    public function register(array $data)
    {
        $user = app(config('auth.providers.users.model'));
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->status = config('laravolt.auth.activation.enable') ?
            config('laravolt.auth.activation.status_before') :
            config('laravolt.auth.registration.status');
        $user->save();

        return $user;
    }

    public function notify(Model $user, $token)
    {
        Mail::to($user)->send(new AccountActivationMail($token));
    }

    public function activate($token)
    {
        $userIds = \DB::table('users_activation')->whereToken($token)->pluck('user_id');

        if ($userIds->isEmpty()) {
            abort(404);
        }

        $userId = $userIds->first();

        $user = app(config('auth.providers.users.model'))->findOrFail($userId);
        $user->status = config('laravolt.auth.activation.status_after');
        $user->save();

        \DB::table('users_activation')->whereUserId($userId)->delete();

        return redirect()->route('auth::login')->withSuccess(trans('laravolt::auth.activation_success'));
    }
}
