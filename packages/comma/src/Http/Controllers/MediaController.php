<?php

namespace Laravolt\Comma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MediaController extends Controller
{
    public function index(Request $request)
    {
    }

    public function store(Request $request)
    {
        return response()->json(
            [
                'url' => asset('img/logo.png'),
                'id'  => rand(1, 99999),
            ]
        );
    }
}
