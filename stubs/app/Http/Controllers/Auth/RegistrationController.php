<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class RegistrationController extends Controller
{
    /**
     * Display the registration view.
     */
    public function show(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        /** @var string $password */
        $password = $request->password;

        Auth::login(
            $user = User::query()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'status' => 'ACTIVE',
            ])
        );

        if (config('laravolt.platform.features.verification') === false) {
            $user->markEmailAsVerified();
        }

        event(new Registered($user));

        return redirect(AppServiceProvider::HOME)
            ->with('success', __('Your account successfully created'));
    }
}
