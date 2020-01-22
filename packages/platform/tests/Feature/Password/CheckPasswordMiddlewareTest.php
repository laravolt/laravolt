<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Password;

use Illuminate\Support\Facades\Route;
use Laravolt\Platform\Http\Middleware\CheckPassword;
use Laravolt\Tests\FeatureTest;

class CheckPasswordMiddlewareTest extends FeatureTest
{
    public function testDoNothingForGuest()
    {
        Route::middleware(CheckPassword::class)->get('public', function () {
            return 'public page';
        });

        $this->visit('public')->seeText('public page');
    }

    public function testDoNothingForWhitelistRoutes()
    {
        Route::middleware(CheckPassword::class)->get('public', function () {
            return 'public page';
        });
        Route::middleware(CheckPassword::class)->get('edit-password', function () {
            return 'edit password';
        });

        $user = $this->createUser();
        $user->setPassword('secret', true);
        config()->set('laravolt.password.duration', 1);
        config()->set('laravolt.password.redirect', 'edit-password');
        config()->set('laravolt.password.except', ['public']);
        $this->actingAs($user);

        $this->visit('public')->seeText('public');
    }

    public function testDoNothingForNormalUser()
    {
        Route::middleware(CheckPassword::class)->get('public', function () {
            return 'public page';
        });
        Route::middleware(CheckPassword::class)->get('edit-password', function () {
            return 'edit password';
        });

        $user = $this->createUser();
        config()->set('laravolt.password.duration', 1);
        config()->set('laravolt.password.redirect', 'edit-password');
        $this->actingAs($user);

        $this->visit('public')->seeText('public page');
    }

    public function testRedirectToEditPasswordPage()
    {
        Route::middleware(CheckPassword::class)->get('public', function () {
            return 'public page';
        });
        Route::middleware(CheckPassword::class)->get('edit-password', function () {
            return 'edit password';
        });

        $user = $this->createUser();
        $user->setPassword('secret', true);
        config()->set('laravolt.password.duration', 1);
        config()->set('laravolt.password.redirect', 'edit-password');
        $this->actingAs($user);

        $this->visit('public')->seeText('edit password');
    }
}
