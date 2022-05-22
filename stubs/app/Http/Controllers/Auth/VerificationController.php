<?php

namespace App\Http\Controllers\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class VerificationController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (auth()->user()?->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return view('auth.verify-email');
    }

    public function store(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user instanceof MustVerifyEmail) {
            throw new \Exception(sprintf('User must implement %s', MustVerifyEmail::class));
        }

        return $this->handle($user);
    }

    public function update(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    }

    private function handle(MustVerifyEmail $user): RedirectResponse
    {
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $user->sendEmailVerificationNotification();

        return back()->withSuccess(
            __(
                'Link verifikasi sudah dikirim ke alamat email :email',
                [
                    'email' => $user->getEmailForVerification(),
                ]
            ) ?? ''
        );
    }
}
