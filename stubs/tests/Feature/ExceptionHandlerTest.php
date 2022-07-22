<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Providers\RouteServiceProvider;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Mockery\MockInterface;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_handle_token_mismatch_exception()
    {
        $verifyCsrfMiddleware = \Mockery::mock(
            VerifyCsrfToken::class,
            static function (MockInterface $mock) {
                $mock->shouldReceive('handle')->andThrow(TokenMismatchException::class);
            }
        );

        $this->instance(VerifyCsrfToken::class, $verifyCsrfMiddleware);

        $this->get(route('auth::login.show'));
        $this->post(route('auth::login.store'), ['email' => 'admin@laravolt.dev', 'password' => 'secret'])
            ->assertRedirect(route('auth::login.show'))
            ->assertSessionHas('error');
    }

    /**
     * @test
     */
    public function it_can_handle_authorization_exception()
    {
        Route::get('admin/page', static fn() => "hello")->middleware('can:access-admin');
        Route::get('livewire/foo', static fn() => "hello")->middleware('can:access-admin');

        // web visit
        $this->get('admin/page')->assertStatus(302)->assertRedirect(RouteServiceProvider::HOME);

        // JSON (API) visit
        $this->json('GET', 'admin/page')
            ->assertStatus(403)
            ->assertExactJson(['message' => 'This action is unauthorized.']);

        // Livewire visit
        $this->get('livewire/foo')
            ->assertStatus(403);
    }
}
