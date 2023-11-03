<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstWalletRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:rlst_wallets,name',
            'name_e' => 'required|string|max:100|unique:rlst_wallets,name_e',
            'company_id' =>'nullable',

        ];
    }


}
