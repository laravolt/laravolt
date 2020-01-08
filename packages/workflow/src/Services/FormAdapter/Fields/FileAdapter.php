<?php

namespace Laravolt\Workflow\Services\FormAdapter\Fields;

class FileAdapter extends StringAdapter
{
    protected $type = 'uploader';

    public function toArray()
    {
        return [
            'type' => $this->type,
            'name' => $this->field->field_name,
            'label' => $this->field->field_label,
            'hint' => '',
            'value' => $this->value,
            'readonly' => $this->readonly,
            'limit' => $this->field->field_meta['limit'] ?? null,
            'extensions' => $this->field->field_meta['extensions'] ?? [],
            'validations' => [],
            'attributes' => ['v-model' => $this->field->field_name],
            'fieldAttributes' => $this->attributes,
        ];
    }
}
