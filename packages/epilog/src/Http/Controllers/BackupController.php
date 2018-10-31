<?php

namespace Laravolt\Epilog\Http\Controllers;

use Illuminate\Routing\Controller;

class BackupController extends Controller
{
    public function index()
    {
        return view('epilog::backup.index');
    }
}
