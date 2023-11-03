<?php

namespace Modules\RealEstate\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class MonthYearRule implements Rule
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
        $requestData = request()->input(); // Get all request data
        
        $year = $requestData['building-policy'][0]['year'];
        $month = $requestData['building-policy'][0]['month'];

        $currentYear = Carbon::now()->format('Y');
        
        if($year == $currentYear){
            $currentMonth = Carbon::now()->format('m');
            if($month >= $currentMonth){
                return true;
            }
        }
        else if($year > $currentYear){
            return true;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The year and month must be greater than or equal to the current year and month.';
    }
}
