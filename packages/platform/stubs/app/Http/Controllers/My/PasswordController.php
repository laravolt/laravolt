<?php

namespace App\Http\Controllers\My;

use Illuminate\Routing\Controller;
use Laravolt\Epicentrum\Http\Requests\My\Password\Update;

class PasswordController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit()
    {
        $user = auth()->user();

        return view('my.password.edit', compact('user'));
    }

    public function update(Update $request)
    {
        if (app('hash')->check($request->password_current, auth()->user()->password)) {
            auth()->user()->setPassword($request->password);

            return redirect()->back()->withSuccess(trans('laravolt::message.password_updated'));
        } else {
            return redirect()->back()->withError(trans('laravolt::message.current_password_mismatch'));
        }
    }
}
