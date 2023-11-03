<?php

namespace Modules\RealEstate\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\RealEstate\Entities\RlstBuildingPolicy;


class UniqueBuildingDatePolicyRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $id;

    public function __construct($id)
    {
        $this->id = $id;   
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
        // Check if the combination of building_id, date, policy_id is unique
        // in the rlst_building_policies table.

         // Log the $value and $attribute
         //Log::info('Validation Attribute: ' . $attribute);
         //Log::info('Validation Value: ' . json_encode($value));

         $requestData = request()->input(); // Get all request data

         //print_r("id in requestData =");
         //print_r($this->id);

         //print_r("building_id =", $requestData['building-policy'][0]['building_id']);

         $building_id = $requestData['building-policy'][0]['building_id'];
         $year = $requestData['building-policy'][0]['year'];
         $month = $requestData['building-policy'][0]['month'];
        

        $count = RlstBuildingPolicy::where('id', '!=', $this->id)
            ->where('policy_id', $value)
            ->where('building_id', $building_id)
            ->where('year', $year)
            ->where('month', $month)
            ->count();

        return $count === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The combination of building_id, year, and month must be unique.';
    }
}
