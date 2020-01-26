<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Laravolt\Workflow\Services\FormAdapter\FieldAdapter;

class MultirowAdapter extends FieldAdapter
{
    protected $type = 'tabular';

    public function toArray()
    {
        $form = $this->field->field_meta['form'] ?? null;
        $items = [];
        if ($form) {
            $items = config('workflow.forms.'.$form);
        }

        $data = [];
        $rows = 1;

        if ($this->value) {
            if (is_array($this->value)) {
                $data = $this->value;
            } else {
                $data = $this->retrieveData($items);
            }
            $rows = count($data);
        }

        return [
            'type' => $this->type,
            'name' => $this->field->field_name,
            'label' => $this->field->field_label,
            'rows' => $rows,
            'data' => $data,
            'value' => $this->value,
            'allow_addition' => true,
            'allow_removal' => true,
            'items' => $items,
        ];
    }

    protected function retrieveData($items)
    {
        $value = json_decode($this->value, true);
        $ids = Arr::first(array_values($value));
        $hasManyTable = Arr::first(array_keys($value));
        $columns = collect($items)->pluck('name')->toArray();

        //coba damar
        foreach ($columns as $key => $value) {
            $columns[$key] = str_replace('[]', '', $value);
        }

        if ($ids && $hasManyTable) {
            return DB::table($hasManyTable)
                ->whereIn('id', $ids)
                ->latest()
                ->get($columns)
                ->transform(function ($item) {
                    return (array) $item;
                })->toArray();
        }

        return [];
    }
}
