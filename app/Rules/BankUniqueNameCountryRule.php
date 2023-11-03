<?php

namespace App\Rules;

use App\Models\Country;
use Illuminate\Contracts\Validation\Rule;

class BankUniqueNameCountryRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private $country_id, private $bank_id = null)
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
        // unique in country
        $country = Country::find($this->country_id);
        if ($country){
            if ($country->banks ){
                $bank = $country->banks()->where('name', $value)->first();
                if ($bank) {
                    if ($this->bank_id) {
                        if ($bank->id == $this->bank_id) {
                            return true;
                        }
                    }
                    return false;
                }
            }
            return true;

        }else{
            return false;

        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __("message.field already exists");

    }
}
