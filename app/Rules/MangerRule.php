<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MangerRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        // manger id where is_salesman = 1 in employees

        $manger = \App\Models\Employee::where('id', $value)->where('is_salesman', 'true')->first();
        if ($manger) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not manger.';
    }
}
