<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Models;

class ProcessDefinition extends CamundaModel
{
    public static function byKey($key)
    {
        $processDefinition = new self();
        $processDefinition->key = $key;

        return $processDefinition;
    }

    public static function all()
    {
        $results = (new static())->request('process-definition', 'get');
        $processDefinitions = [];
        foreach ($results as $result) {
            $processDefinitions[] = new static($result->id, $result);
        }

        return $processDefinitions;
    }

    public function startInstance($data = [], $businessKey = null)
    {
        // At least one value must be set...
        if (count($data) == 0) {
            $data['a'] = 'b';
        }

        $payload = [
            'variables' => $this->formatVariables($data),
        ];

        if ($businessKey) {
            $payload['businessKey'] = $businessKey;
        }

        $processDefinition = $this->post('start', $payload, true);

        return new ProcessInstance($processDefinition->id, ['businessKey' => $businessKey]);
    }

    public function xml()
    {
        return $this->get('xml')->bpmn20Xml;
    }

    public function getInfo()
    {
        return $this->get('');
    }

    public function getXml()
    {
        return $this->get('xml');
    }

    public function getStartTaskName()
    {
        $xml = $this->getXml()->bpmn20Xml;

        $parser = new \SimpleXMLElement($xml);
        $parser->registerXPathNamespace('bpmn', 'http://www.omg.org/spec/BPMN/20100524/MODEL');
        $parser->registerXPathNamespace('camunda', 'http://camunda.org/schema/1.0/bpmn');

        $startTaskName = $parser->xpath('//bpmn:startEvent');

        return (string) data_get($startTaskName, '0')['id'] ?? false;
    }
}
