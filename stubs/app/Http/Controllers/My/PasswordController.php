<?php

declare(strict_types=1);

namespace App\Http\Controllers\My;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Hashing\HashManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Laravolt\Epicentrum\Http\Requests\My\Password\Update;

final class PasswordController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        return view('my.password.edit', ['user' => $user]);
    }

    public function update(Update $request): RedirectResponse
    {
        /** @var string $password */
        $password = $request->input('password_current');
        /** @var User $user */
        $user = auth()->user();
        /** @var string $hashedPassword */
        $hashedPassword = $user->password;
        if (resolve(HashManager::class)->check($password, $hashedPassword)) {
            $user->setPassword($request->password);

            return to_route('my::password.edit')->withSuccess(__('laravolt::message.password_updated'));
        }

        return to_route('my::password.edit')->withError(
            __('laravolt::message.current_password_mismatch')
        );
    }
}
