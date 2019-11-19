<?php namespace Laravolt\SemanticForm\Elements;

use Illuminate\Database\Eloquent\Model;

class Uploader extends Input
{
    protected $attributes = [
        'type' => 'file',
        'class' => 'uploader',
        'data-limit' => 1,
    ];

    protected $mediaUrl;

    public function limit(int $limit)
    {
        return $this->data('limit', $limit);
    }

    public function extensions(array $extensions)
    {
        return $this->data('extensions', implode(',', $extensions));
    }

    public function mediaUrl(string $url)
    {
        $this->mediaUrl = $url;
        $this->data('media-url', $url);

        return $this;
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
