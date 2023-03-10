<?php

declare(strict_types=1);

namespace Laravolt\Platform\Controllers;

use Illuminate\Routing\Controller;
use Laravolt\Fields\Field;

class SettingsController extends Controller
{
    public function edit()
    {
        $config = config('laravolt.ui');

        if (str_contains($config['brand_image'], '<svg height="50"')) {
            $config['brand_image'] = null;
        }

        return view('laravolt::settings.edit', compact('config'));
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
