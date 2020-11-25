<?php

namespace Laravolt\DatabaseMonitor\Controllers;

use App\Jobs\BackupData;
use App\Notifications\BackupNotifiable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function index()
    {
        return view('database-monitor::backup.index');
    }

    public function store()
    {
        try {
            // Artisan::call('backup:run', ['--disable-notifications' => true]);
            BackupData::dispatch();

            return redirect()
                ->back()
                ->withInfo(
                    sprintf(
                        'Proses backup sedang dijalankan. Email notifikasi akan dikirimkan ke %s setelah proses selesai.',
                        (new BackupNotifiable())->routeNotificationForMail()
                    )
                );
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }
}
