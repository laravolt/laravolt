<?php

declare(strict_types=1);

namespace Laravolt\Platform\Controllers;

use Illuminate\Routing\Controller;
use Laravolt\Fields\Field;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('laravolt::settings.edit');
    }

    public function update()
    {
        $form = collect(config('laravolt.platform.settings'))->filter(fn($item) => isset($item['name']));
        foreach ($form as $field) {
            $key = $field['name'];
            if ($field['type'] === Field::UPLOADER) {
                setting(["laravolt.ui.$key" => request()->media($key)->first()]);
            } else {
                setting(["laravolt.ui.$key" => request($key, '')]);
            }
        }

        setting()->save();

        return redirect()->back()->withSuccess(__('Application settings updated'));
    }
}
