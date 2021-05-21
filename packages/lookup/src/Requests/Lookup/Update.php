<?php

declare(strict_types=1);

namespace Laravolt\Lookup\Requests\Lookup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;
use Laravolt\Lookup\Models\Lookup;

class Update extends FormRequest
{
    public function rules()
    {
        $table = (new Lookup())->getTable();
        return [
            'collection' => [],
            'parent_key' => [],
            'lookup_key' => ['required', (new Unique($table, 'lookup_key'))->ignore(request()->route('lookup')->getKey())],
            'lookup_value' => ['required'],
        ];
    }
}
