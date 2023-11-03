<?php

namespace Modules\RealEstate\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\RealEstate\Entities\RlstBuildingWallet;
use Illuminate\Support\Facades\Log;



class UniqueBuildingWallet implements Rule
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
        // Check if the combination of building_id and wallet_id is unique
        // in the rlst_building_wallet table.

         // Log the $value and $attribute
         //Log::info('Validation Attribute: ' . $attribute);
         //Log::info('Validation Value: ' . json_encode($value));

         $requestData = request()->input(); // Get all request data

         //print_r("building_id =", $requestData['building-wallet'][0]['building_id']);

         $building_id = $requestData['building-wallet'][0]['building_id'];


        $count = RlstBuildingWallet::where('id', '!=', $this->id)
            ->where('wallet_id', $value)
            ->where('building_id', $building_id)
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
        return 'The combination of owner_id and wallet_id is not unique.';
    }
}
