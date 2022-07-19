<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['laravolt.platform.features.verification' => true]);
    }

    /**
     * @test
     */
    public function it_can_visit_verification_page()
    {
        $this->actingAs(User::factory()->create(['email_verified_at' => null]));

        $this->get(route('verification.notice'))
            ->assertSeeText(__('Verifikasi Email'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function it_cannot_visit_verification_page_if_already_verified()
    {
        $this->actingAs(User::factory()->create(['email_verified_at' => now()]));

        $this->get(route('verification.notice'))
            ->assertRedirect(RouteServiceProvider::HOME);
    }

    /**
     * @test
     */
    public function it_can_resend_verification_email()
    {
        $this->actingAs($user = User::factory()->create(['email_verified_at' => null]));
        Notification::fake();

        $this->post(route('verification.send'))
            ->assertSessionHas('success');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * @test
     */
    public function it_cannot_resend_verification_email_if_already_verified()
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('verification.send'))
            ->assertRedirect(RouteServiceProvider::HOME);
    }

    /**
     * @test
     */
    public function it_can_verify_email()
    {
        $this->actingAs($user = User::factory()->create(['email_verified_at' => null]));

        $this->mock(EmailVerificationRequest::class, function ($mock) use ($user) {
            $mock->shouldReceive('authorize')->andReturnTrue();
            $mock->shouldReceive('user')->andReturn($user);
        });

        $this->withoutMiddleware()
            ->get(route('verification.verify', ['id', 'hash']))
            ->assertRedirect(RouteServiceProvider::HOME.'?verified=1');
    }

    /**
     * @test
     */
    public function it_cannot_verify_email_if_already_verified()
    {
        $this->actingAs($user = User::factory()->create());

        $this->mock(EmailVerificationRequest::class, function ($mock) use ($user) {
            $mock->shouldReceive('authorize')->andReturnTrue();
            $mock->shouldReceive('user')->andReturn($user);
        });

        $this->withoutMiddleware()
            ->get(route('verification.verify', ['id', 'hash']))
            ->assertRedirect(RouteServiceProvider::HOME.'?verified=1');
    }

    /**
     * @test
     */
    public function validate_user_model_concerns()
    {
        config(['app.debug' => true]);
        Auth::shouldReceive('user')->andReturn(new \stdClass());
        $this->withoutMiddleware()->post(route('verification.send'))
            ->assertSeeText(MustVerifyEmail::class)
            ->assertStatus(500);
    }
}
