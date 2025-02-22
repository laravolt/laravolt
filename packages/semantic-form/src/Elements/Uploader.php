<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class Uploader extends Input
{
    protected $attributes = [
        'type' => 'file',
        'class' => 'uploader',
        'data-limit' => 1,
        'data-file-max-size' => 10000,
    ];

    protected $ajax = true;

    protected $mediaUrl;

    protected $fallbackMediaUrl = 'media::store';

    public function limit(int $limit)
    {
        return $this->data('limit', $limit);
    }

    public function fileMaxSize(int $size)
    {
        return $this->data('file-max-size', $size);
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

        if (! $url) {
            $url = Route::has($this->fallbackMediaUrl) ?
                URL::route($this->fallbackMediaUrl, ['handler' => 'fileuploader'])
                : false;
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

        if (is_string($mediaCollection)) {
            $temp = json_decode($mediaCollection);
            if (json_last_error() == JSON_ERROR_NONE) {
                $mediaCollection = $temp;
            } else {
                $mediaCollection = [$mediaCollection];
            }
        }

        if (! is_iterable($mediaCollection)) {
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
                    'data' => [
                        'id' => $media->getKey(),
                    ],
                ];
            } else {
                $imageHeader = @get_headers($media, true);
                $data[] = [
                    'file' => URL::to($media),
                    'name' => basename($media),
                    'size' => $imageHeader['Content-Length'] ?? 0,
                    'type' => $imageHeader['Content-Type'] ?? 'image/jpg',
                    'data' => [
                        // TODO resolve ID from media URL
                        'id' => null,
                    ],
                ];
            }
        }

        $this->data('fileuploader-files', $data);
        $this->value = $data;

        return $this;
    }

    public function displayValue()
    {
        if (is_array($this->value)) {
            $output = "<div class='ui list'>";
            $output .= '</div>';

            foreach ($this->value as $media) {
                $output .= sprintf(
                    "<div class='item'><a href='%s' target='_blank'>%s <i class='icon paperclip'></i></a></div>",
                    $media['file'],
                    $media['name']
                );
            }

            return $output;
        }

        return null;
    }
}
