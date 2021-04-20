<?php

namespace Laravolt\Epicentrum\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest implements \Laravolt\Epicentrum\Contracts\Requests\Account\Store
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
        return [
            'name'     => 'required|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|max:255',
            'status'   => 'required',
        ];
    }
}
