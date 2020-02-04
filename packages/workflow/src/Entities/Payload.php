<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Entities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Laravolt\Workflow\FieldFormatter\CamundaFormatter;
use Laravolt\Workflow\FieldFormatter\DbFormatter;
use Laravolt\Workflow\Models\Form;
use Spatie\DataTransferObject\DataTransferObject;

class Payload extends DataTransferObject
{
    public $taskName;

    /** @var array */
    public $data;

    /** @var Illuminate\Database\Eloquent\Collection|null */
    public $fields;

    public static function make(Module $module, string $taskName, ?array $rawData)
    {
        $fields = Form::getFields($module->processDefinitionKey, $taskName);

        $data = static::mutateData($module, $taskName, $rawData);

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

    protected static function mutateData(Module $module, $taskName, $rawData)
    {
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
