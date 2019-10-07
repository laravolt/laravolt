<?php

namespace Laravolt\Platform\Controllers;

use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravolt\Platform\Concerns\Activation;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, Activation {
        RegistersUsers::register as registerWithoutActivation;
        Activation::register as registerWithActivation;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Registrar instance.
     *
     * @var \Laravolt\Auth\Contracts\UserRegistrar
     */
    protected $registrar;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = config('laravolt.auth.redirect.after_register', '/');
        $this->middleware('guest');

        $this->registrar = app('laravolt.auth.registrar');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('laravolt::register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        if (config('laravolt.auth.activation.enable')) {
            return $this->registerWithActivation($request);
        }

        return $this->registerWithoutActivation($request);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return $this->registrar->validate($data);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
        return $this->registrar->register($data);
    }

    /**
     * The user has been registered.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed                    $user
     *
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        if (method_exists($this->registrar, 'registered')) {
            return $this->registrar->registered($request, $user);
        }
    }
}
