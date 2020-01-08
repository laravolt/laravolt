<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

class Deployment extends CamundaModel
{
    public function create($name, $file)
    {
        $filename = pathinfo($file)['basename'];

        $files[] = [
            'name' => $name,
            'contents' => file_get_contents($file),
            'filename' => $filename,
        ];

        $multipart = [
            [
                'name' => 'deployment-name',
                'contents' => $name,
            ],
            [
                'name' => 'tenant-id',
                'contents' => config('camunda.api.tenant-id'),
            ],
        ];

        $this->post('deployment/create', [
            'multipart' => array_merge($multipart, $files),
        ]);
    }
}
