<?php

namespace Laravolt\Comma\Http\Controllers;

use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        return view('comma::categories.index');
    }
}
