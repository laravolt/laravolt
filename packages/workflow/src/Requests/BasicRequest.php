<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laravolt\Workflow\Models\Form;

class BasicRequest extends FormRequest
{
    protected $fields;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $processDefinitionKey = $this->input('_process_definition_key');
        $taskName = $this->input('_task_name');

        $this->fields = Form::query()
            ->where('process_definition_key', $processDefinitionKey)
            ->where('task_name', $taskName)
            ->get();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // skip validations for DRAFT
        if ($this->isDraft()) {
            return [];
        }

        $rules = $this->fields->mapWithKeys(
            function ($item) {
                if ($item['field_type'] === 'multirow') {
                    return $item['field_meta']['validation'] ?? null;
                }

                return [$item['field_name'] => $item['field_meta']['validation'] ?? null];
            }
        )->filter()->toArray();

        return $rules;
    }

    public function attributes()
    {
        return $this->fields->pluck('field_label', 'field_name')->toArray();
    }

    public function isDraft()
    {
        return (bool) $this->input('_draft');
    }
}
