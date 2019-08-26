<?php namespace Laravolt\SemanticForm\Elements;

use Illuminate\Database\Eloquent\Model;

class Uploader extends Input
{
    protected $attributes = [
        'type' => 'file',
        'class' => 'uploader',
    ];

    protected function setValue($media)
    {
        if ($media instanceof Model) {
            $value = [
                [
                    'file' => $media->getFullUrl(),
                    'name' => $media->file_name,
                    'size' => $media->size,
                    'type' => $media->mime_type,
                    "data" => [
                        "url" => $media->getFullUrl(),
                        "thumbnail" => $media->getFullUrl(),
                        "readerForce" => true,
                    ],
                ],
            ];
            $this->data('fileuploader-files', htmlspecialchars(json_encode($value)));
        }

        return $this;
    }
}
