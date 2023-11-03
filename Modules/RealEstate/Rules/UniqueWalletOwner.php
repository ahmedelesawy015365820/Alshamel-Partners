<?php

namespace Modules\RealEstate\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\RealEstate\Entities\RlstWalletOwner;
use Illuminate\Support\Facades\Log;

class UniqueWalletOwner implements Rule
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
        // Check if the combination of owner_id and wallet_id is unique
        // in the rlst_wallet_owners table.

         // Log the $value and $attribute
         //Log::info('Validation Attribute: ' . $attribute);
         //Log::info('Validation Value: ' . json_encode($value));



         $requestData = request()->input(); // Get all request data

         //print_r("wallet_id =", $requestData['wallet-owner'][0]['wallet_id']);

         $wallet_id = $requestData['wallet-owner'][0]['wallet_id'];


        $count = RlstWalletOwner::where('id', '!=', $this->id)
            ->where('owner_id', $value)
            ->where('wallet_id', $wallet_id)
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
