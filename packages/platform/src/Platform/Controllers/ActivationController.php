<?php

namespace Laravolt\Platform\Controllers;

use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Routing\Controller;
use Laravolt\Platform\Concerns\Activation;

class ActivationController extends Controller
{
    use Activation, RegistersUsers {
        Activation::register insteadof RegistersUsers;
    }
}
