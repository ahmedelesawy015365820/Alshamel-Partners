<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NationalIdRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private $country_id = 1)
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
        $country_id = \App\Models\Country::find(
            $this->country_id
        );

        if ($country_id) {
            if ($country_id->national_id_length == strlen($value)) {
                return true;
            }
        }

        return false;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'يجب ان يكون الرقم القومي ' . \App\Models\Country::find(
            $this->country_id
        )->national_id_length . ' ارقام';
    }
}
