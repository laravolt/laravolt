<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Controllers;

use Laravolt\Camunda\Models\ProcessInstanceHistory;

class ProcessXmlController
{
    public function __invoke($id)
    {
        $processInstance = (new ProcessInstanceHistory($id))->fetch();
        $xml = $processInstance->processDefinition()->xml();

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
