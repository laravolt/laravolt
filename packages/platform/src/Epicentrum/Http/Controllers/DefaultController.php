<?php

namespace Laravolt\Epicentrum\Http\Controllers;

use Illuminate\Routing\Controller;

class DefaultController extends Controller
{
    public function index()
    {
        return redirect()->route('epicentrum::users.index');
    }
}
