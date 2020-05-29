<?php

declare(strict_types=1);

namespace Laravolt\Platform\Controllers;

use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('laravolt::settings.edit');
    }

    public function update()
    {
        return redirect()->back()->withSuccess(__('Application settings updated'));
    }
}
