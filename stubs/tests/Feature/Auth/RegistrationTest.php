<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use InteractsWithDatabase;
    use LazilyRefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_registration_page()
    {
        $this->get(route('auth::registration.show'))
            ->assertOk()
            ->assertSeeText(__('Name'))
            ->assertSeeText(__('Email'))
            ->assertSeeText(__('Password'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_handle_correct_registration()
    {
        $payload = [
            'name' => 'Jon Dodo',
            'email' => 'jon@laravolt.dev',
            'password' => 'asdf1234',
            'password_confirmation' => 'asdf1234',
        ];

        $response = $this->post(route('auth::registration.store'), $payload);
        $response->assertSessionHas('success')
            ->assertRedirect(AppServiceProvider::HOME);

        $this->assertDatabaseHas('users', collect($payload)->only(['name', 'email'])->toArray());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_handle_correct_registration_with_activation()
    {
        Notification::fake();
        $email = 'jon@laravolt.dev';

        $payload = [
            'name' => 'Jon Dodo',
            'email' => $email,
            'password' => 'asdf1234',
            'password_confirmation' => 'asdf1234',
        ];

        $response = $this->post(route('auth::registration.store'), $payload);
        $response->assertSessionHas('success')
            ->assertRedirect(AppServiceProvider::HOME);

        $this->assertDatabaseHas('users', collect($payload)->only(['name', 'email'])->toArray());

        Notification::assertSentTo(User::first(), VerifyEmail::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_auto_verify_email()
    {
        config(['laravolt.platform.features.verification' => false]);
        Notification::fake();
        $email = 'jon@laravolt.dev';

        $payload = [
            'name' => 'Jon Dodo',
            'email' => $email,
            'password' => 'asdf1234',
            'password_confirmation' => 'asdf1234',
        ];

        $this->post(route('auth::registration.store'), $payload);

        $this->assertDatabaseMissing(
            'users',
            collect($payload)->only(['name', 'email'])->toArray() + ['email_verified_at' => null]
        );

        Notification::assertNotSentTo(User::first(), VerifyEmail::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_errors_if_failed()
    {
        $this->post(route('auth::registration.store'))
            ->assertSessionHasErrors();
    }
}
