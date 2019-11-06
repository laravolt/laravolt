<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravolt\SemanticForm\Elements\Segments;
use Laravolt\SemanticForm\Elements\SegmentTitle;

class FieldCollection extends Collection
{
    protected $fieldMethod = [
        'options', 'api', 'ajax', 'query', 'fieldLabel', 'hint', 'limit', 'extensions',
    ];

    public function __construct($fields = [])
    {
        $items = [];
        foreach ($fields as $field) {
            if (is_string($field)) {
                $field = ['type' => 'text', 'name' => $field, 'label' => Str::title($field)];
            }

            $field = collect($field);

            $type = $field['type'] ?? null;
            if (in_array($type, ['button', 'submit'])) {
                $element = form()->{$type}($field['label'] ?? null, $field['name'] ?? null);
            } elseif (in_array($type, ['action'])) {
                $children = [];
                foreach ($field['items'] as $child) {
                    $children[] = form()->{$child['type']}($child['label'] ?? null, $child['name'] ?? null);
                }
                $element = form()->{$type}($children);
            } elseif ($type === 'segment') {
                $element = new Segments(new SegmentTitle($field['label']), new FieldCollection($field['items']));
            } else {
                $element = form()
                    ->{$type}($field['name'])
                    ->label($field['label'] ?? null)
                    ->hint($field['hint'] ?? null);
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
