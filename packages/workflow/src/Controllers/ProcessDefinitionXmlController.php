<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Controllers;

use Laravolt\Camunda\Models\ProcessDefinition;

class ProcessDefinitionXmlController
{
    public function __invoke($key)
    {
        $processDefinition = ProcessDefinition::byKey($key)->fetch();
        $xml = $processDefinition->xml();

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
