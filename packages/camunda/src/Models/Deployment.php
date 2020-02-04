<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

class Deployment extends CamundaModel
{
    public function create($name, $bpmnFiles)
    {
        $bpmnFiles = (array) $bpmnFiles;
        $attachments = [];
        foreach ($bpmnFiles as $bpmn) {
            $filename = pathinfo($bpmn)['basename'];

            $attachments[] = [
                'name' => $filename,
                'contents' => file_get_contents($bpmn),
                'filename' => $filename,
            ];
        }

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

        return $this->post('deployment/create', [
            'multipart' => array_merge($multipart, $attachments),
        ]);
    }
}
