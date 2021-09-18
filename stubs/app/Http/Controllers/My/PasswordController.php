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
        if (app('hash')->check($request->password_current ?? '', auth()->user()->password ?? '')) {
            auth()->user()?->setPassword($request->password);

            return redirect()->back()->withSuccess(__('laravolt::message.password_updated') ?? '');
        }

        return redirect()->back()->withError(__('laravolt::message.current_password_mismatch') ?? '');
    }
}
