<?php

namespace Laravolt\Rules;

use Illuminate\Contracts\Validation\Rule;

class Lookup implements Rule
{
    private string $collection;

    /**
     * Create a new rule instance.
     *
     * @param  string  $collection
     */
    public function __construct(string $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        dd($attribute, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
