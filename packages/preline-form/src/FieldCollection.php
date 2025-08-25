<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravolt\Fields\Field;
use Laravolt\PrelineForm\Contracts\HasFormOptions;
use Laravolt\PrelineForm\Elements\Html;

class FieldCollection extends Collection
{
    protected $fieldMethod = [
        'api', 'ajax', 'query', 'fieldLabel', 'fieldAttributes', 'limit', 'extensions', 'fileMaxSize', 'placeholder', 'value',
        'readonly', 'required',
    ];

    public function __construct($fields = [])
    {
        foreach ($fields as $key => $field) {
            if (is_string($field)) {
                $field = ['type' => 'text', 'name' => $field, 'label' => Str::title($field)];
            }

            if ($field instanceof Field) {
                $field = $field->toArray();
            }

            $field['name'] ??= $key;

            $field += ['type' => 'text', 'name' => null, 'label' => null, 'hint' => null, 'attributes' => []];
            $this->put($field['name'], $this->createField($field));
        }
    }

    protected function createField($field)
    {
        $field = collect($field);
        $type = $field['type'];
        $macro = false;

        switch ($type) {
            case 'email':
            case 'hidden':
            case 'number':
            case 'password':
            case 'text':
            case 'textarea':
                $element = preline_form()
                    ->{$type}($field['name'])
                    ->label($field['label'])
                    ->hint($field['hint'])
                    ->attributes($field['attributes']);
                break;

            case 'checkbox':
                $element = preline_form()
                    ->checkbox($field['name'])
                    ->label($field['label'])
                    ->hint($field['hint'])
                    ->attributes($field['attributes']);
                $element->setChecked($field['checked'] ?? false);
                break;

            case 'button':
            case 'submit':
                $element = preline_form()->{$type}($field['label'], $field['name'])->attributes($field['attributes']);
                break;

            case 'radioGroup':
            case 'select':
                $options = $field['options'] ?? [];
                if (is_string($options) && ($model = app($options)) instanceof HasFormOptions) {
                    $options = $model->toFormOptions();
                }

                $element = preline_form()
                    ->{$type}($field['name'], $options, $field['value'] ?? null)
                    ->label($field['label'])
                    ->hint($field['hint'])
                    ->attributes($field['attributes']);
                break;

            case 'checkboxGroup':
                $options = $field['options'] ?? [];
                if (is_string($options) && ($model = app($options)) instanceof HasFormOptions) {
                    $options = $model->toFormOptions();
                }

                $element = preline_form()
                    ->checkboxGroup($field['name'], $options, $field['value'] ?? [])
                    ->label($field['label'])
                    ->hint($field['hint'])
                    ->attributes($field['attributes']);
                break;

            case 'file':
                $element = preline_form()
                    ->file($field['name'])
                    ->label($field['label'])
                    ->hint($field['hint'])
                    ->attributes($field['attributes']);
                break;

            case 'html':
                $element = new Html(Arr::get($field, 'content'));
                $element->label($field['label'] ?? null);
                break;

            default:
                if (! PrelineForm::hasMacro($type)) {
                    throw new \InvalidArgumentException(sprintf('Method atau macro %s belum didefinisikan', $type));
                }
                $element = preline_form()->{$type}($field->toArray());
                $macro = true;
                break;
        }

        $field = $this->applyRequiredValidation($field);

        if (! $macro) {
            foreach ($field->only($this->fieldMethod) as $method => $param) {
                if ($param !== null && method_exists($element, $method)) {
                    $element->{$method}($param);
                }
            }

            $element->addClass($field['class'] ?? '');
        }

        return $element;
    }

    public function render()
    {
        $form = '';
        foreach ($this->items as $item) {
            $form .= (string) $item;
        }

        return $form;
    }

    public function bindValues(array $values)
    {
        foreach ($values as $key => $value) {
            if (($element = $this->get($key)) !== null) {
                if (method_exists($element, 'setChecked')) {
                    $element->setChecked($value);
                } elseif (method_exists($element, 'value')) {
                    $element->value($value);
                }
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->render();
    }

    private function applyRequiredValidation($field)
    {
        $validations = $field->get('rules') ?? $field->get('validations') ?? [];

        if (is_string($validations)) {
            $validations = explode('|', $validations);
        }

        if (in_array('required', $validations)) {
            $field['required'] = true;
        }

        return $field;
    }
}