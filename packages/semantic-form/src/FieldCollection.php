<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravolt\SemanticForm\Elements\Html;
use Laravolt\SemanticForm\Elements\Segments;
use Laravolt\SemanticForm\Elements\SegmentTitle;

class FieldCollection extends Collection
{
    protected $fieldMethod = [
        'api', 'ajax', 'query', 'fieldLabel', 'hint', 'limit', 'extensions', 'placeholder',
    ];

    public function __construct($fields = [])
    {
        $items = [];
        foreach ($fields as $field) {
            if (is_string($field)) {
                $field = ['type' => 'text', 'name' => $field, 'label' => Str::title($field)];
            }

            $field = $field + ['type' => 'text', 'name' => null, 'label' => null, 'hint' => null];
            $items[] = $this->createField($field);
        }

        $this->items = $items;
    }

    protected function createField($field)
    {
        $field = collect($field);
        $type = $field['type'];
        $macro = false;

        switch ($type) {
            case 'checkbox':
            case 'date':
            case 'email':
            case 'hidden':
            case 'number':
            case 'rupiah':
            case 'text':
            case 'textarea':
            case 'time':
                $element = form()->{$type}($field['name'])->label($field['label'])->hint($field['hint']);
                break;

            case 'button':
            case 'submit':
                $element = form()->{$type}($field['label'], $field['name']);
                break;

            case 'action':
                $children = [];
                foreach ($field['items'] as $child) {
                    $children[] = form()->{$child['type']}($child['label'], $child['name']);
                }
                $element = form()->{$type}($children);
                break;

            case 'checkboxGroup':
            case 'radioGroup':
            case 'dropdown':
                $element = form()
                    ->{$type}($field['name'], $field['options'])
                    ->label($field['label'])
                    ->hint($field['hint']);
                break;

            case 'dropdownDB':
                $element = form()
                    ->dropdownDB(
                        $field['name'],
                        $field['query'],
                        $field['query_key_column'] ?? null,
                        $field['query_display_column'] ?? null
                    )
                    ->dependency($field['dependency'] ?? null)
                    ->label($field['label'])
                    ->hint($field['hint']);
                break;

            case 'segment':
                $element = new Segments(new SegmentTitle($field['label']), new FieldCollection($field['items']));
                break;

            case 'html':
                $element = new Html(Arr::get($field, 'content'));
                break;

            default:
                $element = form()->{$type}($field->toArray());
                $macro = true;
                break;
        }

        if (!$macro) {
            foreach ($field->only($this->fieldMethod) as $method => $param) {
                $element->{$method}($param);
            }
            $element->addClass($field['class'] ?? '');
        }

        return $element;
    }

    public function render()
    {
        $form = "";
        foreach ($this->items as $item) {
            $form .= (string) $item;
        }

        return $form;
    }

    public function __toString()
    {
        return $this->render();
    }
}
