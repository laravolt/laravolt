<?php

namespace Laravolt\Workflow\Services\FormAdapter;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravolt\SemanticForm\SemanticForm;
use Laravolt\Workflow\Services\FormAdapter\Fields\MacroAdapter;
use Laravolt\Workflow\Services\FormAdapter\Fields\StringAdapter;

class FormAdapter
{
    protected $localFields = [];

    protected $values = [];

    protected $readonly = false;

    public static $types = [
        'autofill' => 'Autofill',
        'boolean' => 'Boolean',
        'date' => 'Date',
        'dropdown' => 'Dropdown',
        'dropdownDB' => 'DropdownDB',
        'email' => 'Email',
        'file' => 'File',
        'hidden' => 'Hidden',
        'html' => 'HTML',
        'image' => 'Image',
        'integer' => 'Integer',
        'multirow' => 'Multirow',
        'number' => 'Number',
        'radio' => 'Radio',
        'rupiah' => 'Rupiah',
        'score' => 'Score',
        'string' => 'String',
        'text' => 'Text (Single Line)',
        'textarea' => 'Textarea (Multi Line)',
        'time' => 'Time',
        'texteditor' => 'WYSIWYG',
    ];

    /**
     * FormDefinitionAdapter constructor.
     *
     * @param Collection $localFields
     * @param array      $values
     */
    public function __construct(Collection $localFields, $values = [])
    {
        $this->localFields = $localFields;
        $this->values = collect($values);
    }

    public function readonly()
    {
        $this->readonly = true;

        return $this;
    }

    public function toArray()
    {
        // Group and sort by segment order
        $groupedFields = $this->localFields->groupBy('segment_group')->transform(function ($item, $key) {
            return [
                'order' => optional($item->first())->segment_order ?? 999,
                'items' => $item,
            ];
        })->sortBy(function ($item, $key) {
            return $item['order'];
        });

        $definition = collect();
        foreach ($groupedFields as $segment => $fields) {
            $transformedFields = $this->transformFields($fields['items'], $segment);
            if ($segment) {
                $definition->push($transformedFields);
            } else {
                foreach ($transformedFields as $field) {
                    $definition->push($field);
                }
            }
        }

        return $definition->toArray();
    }

    protected function transformFields($fields, $segment)
    {
        $definition = [];
        foreach ($fields as $field) {
            $type = Str::studly($field->field_type);
            $value = $this->values->get($field->field_name) ?? Arr::get($field->field_meta, 'value');

            $adapter = "\\Laravolt\\Workflow\\Services\\FormAdapter\\Fields\\{$type}Adapter";
            if (!class_exists($adapter)) {
                $macro = lcfirst($type);

                if (SemanticForm::hasMacro($macro)) {
                    $definition[] = (new MacroAdapter($field, $value, $this->readonly))
                        ->setType($macro)
                        ->toArray();
                } else {
                    $definition[] = (new StringAdapter($field, $value, $this->readonly))->toArray();
                }
            } else {
                $definition[] = (new $adapter($field, $value, $this->readonly))->toArray();
            }
        }

        if ($segment) {
            return [
                'type' => 'segment',
                'label' => $segment,
                'items' => $definition,
            ];
        }

        return $definition;
    }
}
