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
        $keys = collect(config('laravolt.platform.settings'))->pluck('name')->filter()->toArray();
        foreach ($keys as $key) {
            setting(["laravolt.ui.$key" => request($key, '')]);
        }

        setting()->save();

        return redirect()->back()->withSuccess(__('Application settings updated'));
    }
}
