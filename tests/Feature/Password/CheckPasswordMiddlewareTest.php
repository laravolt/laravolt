<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Password;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravolt\Middleware\CheckPassword;
use Laravolt\Tests\FeatureTest;

class CheckPasswordMiddlewareTest extends FeatureTest
{
    use RefreshDatabase;

    public function test_do_nothing_for_guest()
    {
        Route::middleware(CheckPassword::class)->get('public', function () {
            return 'public page';
        });

        $this->visit('public')->seeText('public page');
    }

    public function test_do_nothing_for_whitelist_routes()
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

    public function test_do_nothing_for_normal_user()
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

    public function test_redirect_to_edit_password_page()
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
