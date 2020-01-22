<?php

declare(strict_types=1);

namespace Laravolt\Menu\Requests\Menu;

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
            'parent_id' => [],
            'label' => ['required'],
            'url' => ['required'],
            'type' => [],
            'order' => [],
            'icon' => [],
            'permission' => [],
            'roles' => [],
            'color' => [],
        ];
    }
}
