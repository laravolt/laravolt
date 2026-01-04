<?php

declare(strict_types=1);

namespace Laravolt\DatabaseMonitor\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Laravolt\DatabaseMonitor\Jobs\BackupDatabaseJob;

class BackupController extends Controller
{
    public function index()
    {
        $disk = config('laravolt.database-monitor.disk');

        return view('database-monitor::backup.index', compact('disk'));
    }

    public function store()
    {
        try {
            BackupDatabaseJob::dispatch();

            return redirect()
                ->back()
                ->withInfo(
                    sprintf(
                        'Proses backup sedang dijalankan. Email notifikasi akan dikirimkan ke %s setelah proses selesai.',
                        auth()->user()->email
                    )
                );
        } catch (Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }
}
