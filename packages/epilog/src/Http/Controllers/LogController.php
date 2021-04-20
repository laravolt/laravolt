<?php

namespace Laravolt\Epilog\Http\Controllers;

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
        $selectedFile = urldecode(request('file'));

        $files = $this->epilog->files();

        if (!$selectedFile) {
            $selectedFile = $files->first()['path'];
        }

        $logs = $this->epilog->logs($selectedFile);

        return view('epilog::log.index', compact('files', 'logs', 'selectedFile'));
    }
}
