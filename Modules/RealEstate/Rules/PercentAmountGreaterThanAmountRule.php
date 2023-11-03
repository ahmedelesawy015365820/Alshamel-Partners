<?php

namespace Modules\RealEstate\Rules;

use Illuminate\Contracts\Validation\Rule;

class PercentAmountGreaterThanAmountRule implements Rule
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
        // Check if the percent_amount is greater than amount

         // Log the $value and $attribute
         //Log::info('Validation Attribute: ' . $attribute);
         //Log::info('Validation Value: ' . json_encode($value));

         $requestData = request()->input(); // Get all request data

         //print_r("building_id =", $requestData['building-policy'][0]['building_id']);

        if(
        isset($requestData['building-policy'][0]['amount']) 
        &&
        isset($requestData['building-policy'][0]['percent_amount'])
        ){
            $amount = $requestData['building-policy'][0]['amount'];
            $percent_amount = $requestData['building-policy'][0]['percent_amount'];

            return $percent_amount > $amount;
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
        return 'percent amount MUST be greater than amount.';
    }
}
