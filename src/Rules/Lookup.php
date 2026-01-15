<?php

declare(strict_types=1);

namespace Laravolt\Rules;

use Illuminate\Contracts\Validation\Rule;

class Lookup implements Rule
{
    private string $collection;

    /**
     * Create a new rule instance.
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
        return \Laravolt\Lookup\Models\Lookup::query()
            ->fromCollection($this->collection)
            ->where('lookup_key', $value)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.lookup');
    }
}
