<?php

namespace App\Http\Controllers\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use function PHPUnit\Framework\assertInstanceOf;

class VerificationController extends Controller
{
    public function show(): View|RedirectResponse
    {
        /** @var MustVerifyEmail $user */
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return view('auth.verify-email');
    }

    public function store(): RedirectResponse
    {
        /** @var MustVerifyEmail $user */
        $user = auth()->user();
        assertInstanceOf(MustVerifyEmail::class, $user);

        return $this->handle($user);
    }

    public function update(EmailVerificationRequest $request): RedirectResponse
    {
        /** @var MustVerifyEmail $user */
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
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
