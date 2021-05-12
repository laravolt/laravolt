<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Http\Controllers;

use Laravolt\Camunda\Http\ProcessDefinitionClient;

class DefinitionXmlController
{
    public function __invoke(string $id)
    {
        $xml = ProcessDefinitionClient::xml($id);

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
