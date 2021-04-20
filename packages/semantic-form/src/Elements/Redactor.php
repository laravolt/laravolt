<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Facades\Route;

class Redactor extends TextArea
{
    protected $mediaUrl;

    protected $fallbackMediaUrl = 'media::store';

    public function mediaUrl(string $url)
    {
        $this->mediaUrl = $url;

        return $this;
    }

    protected function beforeRender()
    {
        $url = $this->mediaUrl;

        if (!$url) {
            $url = Route::has($this->fallbackMediaUrl) ? route($this->fallbackMediaUrl) : false;
        }
        if ($url) {
            $this->data('upload-url', $url);
        }
    }
}
