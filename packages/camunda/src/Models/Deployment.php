<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

class Deployment extends CamundaModel
{
    public static function all()
    {
        $results = (new static())->request('deployment', 'get');
        $deployments = [];
        foreach ($results as $result) {
            $deployments[] = new static($result->id, $result);
        }

        return $deployments;
    }

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
        ];

        if ($this->tenantId) {
            $multipart[] = [
                'name' => 'tenant-id',
                'contents' => $this->tenantId,
            ];
        }

        return $this->post('deployment/create', [
            'multipart' => array_merge($multipart, $attachments),
        ]);
    }
}
