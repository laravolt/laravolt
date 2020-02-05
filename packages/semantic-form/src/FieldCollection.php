<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravolt\SemanticForm\Elements\ActionWrapper;
use Laravolt\SemanticForm\Elements\FormControl;
use Laravolt\SemanticForm\Elements\Html;
use Laravolt\SemanticForm\Elements\Segments;
use Laravolt\SemanticForm\Elements\SegmentTitle;

class FieldCollection extends Collection
{
    protected $fieldMethod = [
        'api', 'ajax', 'query', 'fieldLabel', 'fieldAttributes', 'limit', 'extensions', 'placeholder', 'value',
        'readonly', 'required',
    ];

    public function __construct($fields = [])
    {
        foreach ($fields as $field) {
            if (is_string($field)) {
                $field = ['type' => 'text', 'name' => $field, 'label' => Str::title($field)];
            }

            $field = $field + ['type' => 'text', 'name' => null, 'label' => null, 'hint' => null, 'attributes' => []];
            $this->put($field['name'], $this->createField($field));
        }
    }

    protected function createField($field)
    {
        $field = collect($field);
        $type = $field['type'];
        $macro = false;

        switch ($type) {
            case 'date':
            case 'email':
            case 'hidden':
            case 'number':
            case 'redactor':
            case 'rupiah':
            case 'text':
            case 'textarea':
            case 'time':
            case 'uploader':
                $element = form()
                    ->{$type}($field['name'])
                    ->label($field['label'])
                    ->hint($field['hint'])
                    ->attributes($field['attributes']);
                if (isset($field['ajax'])) {
                    $element->ajax($field['ajax']);
                }
                break;

            case 'datepicker':
                $element = form()
                    ->{$type}($field['name'])
                    ->label($field['label'])
                    ->hint($field['hint'])
                    ->attributes($field['attributes']);
                if (isset($field['format'])) {
                    $element->format($field['format']);
                }
                break;

            case 'checkbox':
                $element = form()
                    ->checkbox($field['name'])
                    ->label($field['label'])
                    ->hint($field['hint'])
                    ->attributes($field['attributes']);
                $element->setChecked($field['checked'] ?? false);
                break;

            case 'button':
            case 'submit':
                $element = form()->{$type}($field['label'], $field['name'])->attributes($field['attributes']);
                break;

            case 'action':
                $children = [];
                foreach ($field['items'] as $child) {
                    $children[] = form()
                        ->{$child['type']}($child['label'], $child['name'])
                        ->attributes($child['attributes'] ?? []);
                }
                $element = form()->{$type}($children);
                break;

            case 'tabular':
                $element = form()->tabular($field['name'], $field['items'])->label($field['label'] ?? null);
                if (isset($field['allow_addition'])) {
                    $element->allowAddition($field['allow_addition']);
                }
                if (isset($field['allow_removal'])) {
                    $element->allowRemoval($field['allow_removal']);
                }
                if (isset($field['rows'])) {
                    $element->rows($field['rows']);
                }

                $element->source($field['data'] ?? []);
                break;

            case 'checkboxGroup':
            case 'radioGroup':
            case 'dropdown':
                $element = form()
                    ->{$type}($field['name'], $field['options'])
                    ->label($field['label'])
                    ->hint($field['hint'])
                    ->attributes($field['attributes']);
                break;

            case 'dropdownDB':
                $element = form()
                    ->dropdownDB(
                        $field['name'],
                        $field['query'],
                        $field['query_key_column'] ?? null,
                        $field['query_display_column'] ?? null
                    )
                    ->label($field['label'])
                    ->hint($field['hint'])
                    ->attributes($field['attributes']);
                if ($field['dependency'] ?? false) {
                    $dependency = $this->get($field['dependency']);
                    if ($dependency instanceof FormControl) {
                        $element->dependency($field['dependency'], $dependency->getValue());
                    }
                }
                break;

            case 'segment':
                $element = new Segments(new SegmentTitle($field['label']), new self($field['items']));
                break;

            case 'html':
                $element = new Html(Arr::get($field, 'content'));
                $element->label($field['label'] ?? null);
                break;

            default:
                if (!SemanticForm::hasMacro($type)) {
                    throw new \InvalidArgumentException(sprintf('Method atau macro %s belum didefinisikan', $type));
                }
                $element = form()->{$type}($field->toArray());
                $macro = true;
                break;
        }

        if (!$macro) {
            foreach ($field->only($this->fieldMethod) as $method => $param) {
                if ($param !== null) {
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

    public function display()
    {
        $items = collect($this->items)->reject(function ($item) {
            return $item instanceof ActionWrapper;
        });

        $table = "<table class='ui definition table'>";
        $table .= '<tbody>';

        $i = 0;
        foreach ($items as $item) {
            $i++;
            if ($item instanceof Segments) {
                $table .= '</tbody>';
                $table .= '</table>';
            }

            $table .= $item->display();

            if ($item instanceof Segments && $i < count($items)) {
                $table .= "<table class='ui definition table'>";
                $table .= '<tbody>';
            }
        }
        $table .= '</tbody>';
        $table .= '</table>';

        return $table;
    }

    public function __toString()
    {
        return $this->render();
    }
}
