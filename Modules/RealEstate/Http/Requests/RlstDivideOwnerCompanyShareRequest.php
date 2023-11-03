<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\RealEstate\Rules\CheckPolicyDetailsRule;

class RlstDivideOwnerCompanyShareRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'building_policy_id' => ['required','integer','exists:rlst_building_policies,id', new CheckPolicyDetailsRule],
            'accrued_revenues' => 'sometimes|numeric|gt:0',
            'actual_revenues' => 'sometimes|numeric|gt:0',
            'expenses_amount' => 'sometimes|numeric|min:0',
            'extra_revenues'=> 'sometimes|numeric|min:0'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
