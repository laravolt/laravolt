<?php

declare(strict_types=1);

namespace Laravolt\Lookup\Requests\Lookup;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'collection' => [],
            'parent_key' => [],
            'lookup_key' => ['required'],
            'lookup_value' => ['required'],
            'description' => [],
        ];
    }
}
