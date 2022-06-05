<?php

namespace App\Http\Controllers\My;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Laravolt\Epicentrum\Http\Requests\My\Password\Update;

class PasswordController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        return view('my.password.edit', compact('user'));
    }

    public function update(Update $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (
            app('hash')->check(
                (string) $request->input('password_current'),
                $user->password
            )
        ) {
            $user->setPassword($request->password);

            return redirect()->route('my::password.edit')->withSuccess(__('laravolt::message.password_updated') ?? '');
        }

        return redirect()->route('my::password.edit')->withError(
            __('laravolt::message.current_password_mismatch') ?? ''
        );
    }
}
