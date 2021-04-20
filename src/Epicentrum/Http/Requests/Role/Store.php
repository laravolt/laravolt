<?php

namespace Laravolt\Epicentrum\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $table = app(config('laravolt.epicentrum.models.role'))->getTable();

        return [
            'name'        => "required|unique:$table",
        ];
    }
}
