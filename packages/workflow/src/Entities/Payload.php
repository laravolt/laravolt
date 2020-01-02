<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Entities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Laravolt\Camunda\FieldFormatter\CamundaFormatter;
use Laravolt\Camunda\FieldFormatter\DbFormatter;
use Laravolt\Camunda\Models\Form;
use Spatie\DataTransferObject\DataTransferObject;

class Payload extends DataTransferObject
{
    public $taskName;

    /** @var array */
    public $data;

    /** @var Illuminate\Database\Eloquent\Collection */
    public $fields;

    public static function make(string $processDefinitionKey, string $taskName, ?array $rawData)
    {
        $fields = Form::getFields($processDefinitionKey, $taskName);

        $data = static::mutateData($taskName, $rawData);

        return new self([
            'taskName' => $taskName,
            'data' => $data,
            'fields' => $fields,
        ]);
    }

    public function getBusinessKey()
    {
        $businessKeyColumn = config('laravolt.workflow.business_key');
        $businessKey = Arr::get($this->data, $businessKeyColumn);

        return $businessKey;
    }

    public function toCamundaVariables()
    {
        return CamundaFormatter::format($this->data, $this->fields);
    }

    public function toTaskFields()
    {
    }

    public function toFormFields()
    {
        return $this->fields
            ->groupBy('form_name')
            ->transform(function (Collection $fields) {
                return [
                    'type' => $fields->first()->type,
                    'fields' => DbFormatter::format($this->data, $fields),
                ];
            })
            ->sortBy('type');
    }

    public static function mutateData($taskName, $rawData)
    {
        $module = request()->route('module');
        $task = $module->getTask($taskName);

        $data = [];
        foreach ($rawData as $key => $value) {
            $services = Arr::get($task, "mutators.$key", []);
            foreach ($services as $serviceKey => $serviceValue) {
                $class = $serviceValue;
                $config = [];

                if (is_string($serviceKey) && is_array($serviceValue)) {
                    $class = $serviceKey;
                    $config = $serviceValue;
                }

                if (class_exists($class)) {
                    $service = new $class($config);
                    $value = $service->execute($value);
                }
            }
            $data[$key] = $value;
        }

        return $data;
    }
}
