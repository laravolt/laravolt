<?php namespace Laravolt\SemanticForm\Elements;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class Uploader extends Input
{
    protected $attributes = [
        'type' => 'file',
        'class' => 'uploader',
        'data-limit' => 1,
    ];

    protected $ajax = true;

    protected $mediaUrl;

    protected $fallbackMediaUrl = 'media::store';

    public function limit(int $limit)
    {
        return $this->data('limit', $limit);
    }

    public function extensions(array $extensions)
    {
        return $this->data('extensions', implode(',', $extensions));
    }

    public function ajax($ajax = true)
    {
        $this->ajax = $ajax;

        return $this;
    }

    public function mediaUrl(string $url)
    {
        $this->mediaUrl = $url;

        return $this;
    }

    protected function beforeRender()
    {
        $url = $this->mediaUrl;

        if (!$url) {
            $url = Route::has($this->fallbackMediaUrl) ? URL::route($this->fallbackMediaUrl,
                ['handler' => 'fileuploader']) : false;
        }

        if ($this->ajax && $url) {
            $this->data('media-url', $url);
        }
    }

    protected function setValue($mediaCollection)
    {
        if ($mediaCollection instanceof Model) {
            $mediaCollection = [$mediaCollection];
        }

        if (!is_iterable($mediaCollection)) {
            return $this;
        }

        $data = [];
        foreach ($mediaCollection as $media) {
            if ($media instanceof Model) {
                $data[] = [
                    'file' => $media->getFullUrl(),
                    'name' => $media->file_name,
                    'size' => $media->size,
                    'type' => $media->mime_type,
                    "data" => [
                        'id' => $media->getKey(),
                    ],
                ];
            }
        }

        $this->data('fileuploader-files', htmlspecialchars(json_encode($data)));

        return $this;
    }
}
