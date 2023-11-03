<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\RealEstate\Rules\UniqueBuildingWallet;
use Illuminate\Validation\Rule;


class RlstBuildingWalletRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        return [
            "building-wallet" => 'required|array',
            "building-wallet.*.building_id" => ['required','integer','exists:rlst_buildings,id,deleted_at,NULL'],
            "building-wallet.*.wallet_id" => ['required','integer','exists:rlst_wallets,id', new UniqueBuildingWallet($this->id)],
            "building-wallet.*.company_id"=>'nullable',
        ];
    }

}
