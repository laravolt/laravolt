<?php

namespace Laravolt\Workflow\Services\FormAdapter;

abstract class FieldAdapter implements FieldAdapterInterface
{
    protected $field;

    protected $value;

    protected $readonly;

    protected $attributes;

    protected $type = 'text';

    /**
     * Field constructor.
     *
     * @param $field
     * @param $value
     */
    public function __construct($field, $value = null, $readonly = false)
    {
        $this->field = $field;
        $this->value = $value;
        $this->readonly = $readonly;

        foreach ($this->field->field_meta['attributes'] ?? [] as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    public function toArray()
    {
        return [
            'type' => $this->type,
            'name' => $this->field->field_name,
            'label' => $this->field->field_label,
            'hint' => $this->field->field_hint,
            'value' => $this->value,
            'readonly' => $this->readonly,
            'required' => in_array('required', $this->field->validation_rules),
            'validations' => $this->field->field_meta['validation'] ?? [],
            'placeholder' => $this->field->field_meta['placeholder'] ?? null,
            'attributes' => ['v-model' => $this->field->field_name],
            'fieldAttributes' => $this->attributes,
        ];
    }
}
