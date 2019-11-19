<?php namespace Laravolt\SemanticForm\Elements;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;

class Redactor extends TextArea
{
    protected $uploadUrl;

    protected $fallbackUploadUrl = 'platform::media.store';

    public function uploadUrl(string $url)
    {
        $this->uploadUrl = $url;

        return $this;
    }

    protected function beforeRender()
    {
        $url = $this->uploadUrl;

        if (!$url) {
            $url = Route::has($this->fallbackUploadUrl) ? route($this->fallbackUploadUrl) : false;
        }
        if ($url) {
            $this->data('upload-url', $url);
        }
    }
}
