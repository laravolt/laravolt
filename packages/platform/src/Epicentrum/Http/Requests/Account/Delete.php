<?php

namespace Laravolt\Epicentrum\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class Delete extends FormRequest implements \Laravolt\Epicentrum\Contracts\Requests\Account\Delete
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $userToDelete = $this->segment(3);
        if ($userToDelete == auth()->id()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
