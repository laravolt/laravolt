<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Camunda;

class UploaderFormatter
{
    public function __invoke($key, $value)
    {
        // TODO: remove dummy
        return [
            'value' => 'R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==',
            'type'  => 'File',
            'valueInfo' => [
                'filename' => 'dummy.jpg',
                'mimetype' => 'image/gif',
                'encoding' => 'UTF-8',
            ],
        ];
        // return ['value' => request()->media($key)->toJson(), 'type' => 'String'];
    }
}
