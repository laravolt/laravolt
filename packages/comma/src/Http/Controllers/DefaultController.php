<?php

namespace Laravolt\Comma\Http\Controllers;

use Illuminate\Routing\Controller;

class DefaultController extends Controller
{
    public function index()
    {
        return redirect()->route('comma::posts.index');
    }
}
