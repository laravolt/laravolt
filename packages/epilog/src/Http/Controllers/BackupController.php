<?php

namespace Laravolt\Cockpit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BackupController extends Controller
{
    public function index()
    {
        return view('cockpit::backup.index');
    }
}
