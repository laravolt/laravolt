<?php

namespace Laravolt\Cockpit\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravolt\Epilog\Epilog;

class LogController extends Controller
{

    private $epilog;

    /**
     * LogController constructor.
     */
    public function __construct()
    {
        $this->epilog = new Epilog();
    }

    public function index()
    {
//        $year = request('year', date('Y'));
//        $month = request('month', date('m'));
        $selectedFile = urldecode(request('file'));

        $files = $this->epilog->files();

        if (!$selectedFile) {
            $selectedFile = $files->first()['path'];
        }

        $logs = $this->epilog->logs($selectedFile);

        return view('cockpit::log.index', compact('files', 'logs', 'selectedFile'));
    }
}
