<?php

declare(strict_types=1);

namespace Laravolt\Platform\Controllers;

use Laravolt\Fields\Field;
use Laravolt\Media\MediaInputBag;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $form = collect(config('laravolt.platform.settings'))->filter(fn ($item) => isset($item['name']));
        foreach ($form as $field) {
            $key = $field['name'];
            if ($field['type'] === Field::UPLOADER) {
                try {
                    /** @var MediaInputBag */
                    $mediaID = request()->media($key)->first();

                    /** @var \Illuminate\Database\Eloquent\Model */
                    $model = config('media-library.media_model');
                    /** @var Media */
                    $media = $model::query()->findOrfail($mediaID);
                    $url = $media->getUrl();
                    $url = str_replace(config('app.url'), '', $url);
                    setting(["laravolt.ui.$key" => $url]);
                } catch (ModelNotFoundException $th) {
                    setting(["laravolt.ui.$key" => null]);
                }
            } else {
                setting(["laravolt.ui.$key" => request($key, '')]);
            }
        }

        setting()->save();

        Artisan::call('view:clear');

        return redirect()->back()->withSuccess(__('Application settings updated'));
    }
}
