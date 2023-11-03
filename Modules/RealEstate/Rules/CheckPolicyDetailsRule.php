<?php

namespace Modules\RealEstate\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckPolicyDetailsRule implements Rule
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
        // Check if columns exists in the request

         // Log the $value and $attribute
         //Log::info('Validation Attribute: ' . $attribute);
         //Log::info('Validation Value: ' . json_encode($value));

         $requestData = request()->input(); // Get all request data
         $buildingPolicyId = $requestData['building_policy_id'];

         //print_r("building_id =", $requestData['building-policy'][0]['building_id']);

        // Get the building policy details  
        
        $model = \Modules\RealEstate\Entities\RlstBuildingPolicy::find($buildingPolicyId);

        $policyId = $model->policy_id;
        //print_r("buildingPolicy =", $policyId);



        switch($policyId){
            case 1: // Owner has a fixed amount
                return $model->amount > 0;
            case 2: // Company has a fixed amount or percent
                return $model->amount > 0 && $model->percent > 0;
            case 3: // Company has a fixed percent
                return $model->percent > 0;
            case 4: // Company has a fixed amount
                return $model->amount > 0;
            case 5: // Company has a fixed amount plus a percent if the amount increased beyond a certain value
                return $model->amount > 0 && $model->percent > 0 && $model->percent_amount > 0;
            default:
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
        return 'The validation error message.';
    }
}
