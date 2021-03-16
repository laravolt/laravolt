<?php

namespace Laravolt\Platform\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide this functionality to your appliations.
    |
    */

    use ValidatesRequests;
    use AuthenticatesUsers {
        AuthenticatesUsers::sendFailedLoginResponse as defaultSendFailedLoginResponse;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '';

    /**
     * The custom login contract instance.
     *
     * @var \Laravolt\Auth\Contracts\Login
     */
    protected $login;

    protected $maxAttempts = 5;

    protected $decayMinutes = 1;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);

        $this->redirectTo = config('laravolt.auth.redirect.after_login');

        $this->maxAttempts = config('laravolt.auth.login.max_attempts');

        $this->decayMinutes = config('laravolt.auth.login.decay_minutes');

        $this->login = app('laravolt.auth.login');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('laravolt::auth.login');
    }

    public function store(Request $request)
    {
        return $this->login($request);
    }

    public function destroy(Request $request)
    {
        return $this->logout($request);
    }

    public function username()
    {
        return config('laravolt.auth.identifier');
    }

    protected function validateLogin(Request $request)
    {
        $rules = $this->login->rules($request);
        $this->validate($request, $rules);
    }

    protected function credentials(Request $request)
    {
        return $this->login->credentials($request);
    }

    /**
     * The user has been authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed                    $user
     *
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (method_exists($this->login, 'authenticated')) {
            return $this->login->authenticated($request, $user);
        }
    }

    /**
     * The user has been logged out.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        if (method_exists($this->login, 'loggedOut')) {
            return $this->login->loggedOut($request);
        }
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        if (method_exists($this->login, 'failed')) {
            return $this->login->failed($request);
        }

        return $this->defaultSendFailedLoginResponse($request);
    }
}
