<?php

namespace Laravolt\Platform\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravolt\Auth\Services\LdapService;

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
        login as defaultLogin;
        sendFailedLoginResponse as defaultSendFailedLoginResponse;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Whether LDAP authentication enabled or not.
     *
     * @var bool
     */
    protected $ldapEnabled = false;

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

        $this->ldapEnabled = config('laravolt.auth.ldap.enable');

        $this->maxAttempts = config('laravolt.auth.login.max_attempts');

        $this->decayMinutes = config('laravolt.auth.login.decay_minutes');

        $this->login = app('laravolt.auth.login');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('laravolt::login');
    }

    public function login(Request $request)
    {
        if ($this->ldapEnabled) {
            try {
                return $this->ldapLogin($request);
            } catch (\Exception $e) {
                return $this->defaultLogin($request);
            }
        }

        return $this->defaultLogin($request);
    }

    protected function ldapLogin(Request $request)
    {
        $ldapService = app(LdapService::class);

        $ldapService->resolveUser($this->credentials($request));
        $user = $ldapService->eloquentUser();

        if ($user && auth()->login($user)) {
            $request->merge(['_auth' => 'ldap']);

            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
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
