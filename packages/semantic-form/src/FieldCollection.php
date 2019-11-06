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
        'api', 'ajax', 'query', 'fieldLabel', 'hint', 'limit', 'extensions',
    ];

    public function __construct($fields = [])
    {
        $items = [];
        foreach ($fields as $field) {
            if (is_string($field)) {
                $field = ['type' => 'text', 'name' => $field, 'label' => Str::title($field)];
            }

            $field = $field + ['type' => 'text', 'name' => null, 'label' => null, 'hint' => null];

            $field = collect($field);

            $type = $field['type'];
            if (in_array($type, ['button', 'submit'])) {
                $element = form()->{$type}($field['label'], $field['name']);
            } elseif (in_array($type, ['action'])) {
                $children = [];
                foreach ($field['items'] as $child) {
                    $children[] = form()->{$child['type']}($child['label'], $child['name']);
                }
                $element = form()->{$type}($children);
            } elseif (in_array($type, ['checkboxGroup', 'radioGroup', 'dropdown'])) {
                $element = form()
                    ->{$type}($field['name'], $field['options'])
                    ->label($field['label'])
                    ->hint($field['hint']);
            } elseif ($type == 'dropdownApi') {
                $element = form()
                    ->{$type}($field['name'], $field['api'])
                    ->label($field['label'])
                    ->hint($field['hint']);
            } elseif ($type == 'dropdownQuery') {
                $element = form()
                    ->{$type}($field['name'], $field['query'])
                    ->label($field['label'])
                    ->hint($field['hint']);
            } elseif ($type === 'segment') {
                $element = new Segments(new SegmentTitle($field['label']), new FieldCollection($field['items']));
            } elseif ($type === 'html') {
                $element = new Html(Arr::get($field, 'content'));
            } else {
                $element = form()
                    ->{$type}($field['name'])
                    ->label($field['label'])
                    ->hint($field['hint']);
            }

            foreach ($field->only($this->fieldMethod) as $method => $param) {
                $element->{$method}($param);
            }

            $items[] = $element;
        }

        $this->items = $items;
    }

    public function render()
    {
        $form = "";
        foreach ($this->items as $item) {
            $form .= $item->render();
        }

        return $form;
    }

    public function __toString()
    {
        return $this->render();
    }
}
