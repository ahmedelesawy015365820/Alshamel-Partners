<?php

namespace Modules\RealEstate\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Modules\RealEstate\Rules\UniqueBuildingDatePolicyRule;
use Modules\RealEstate\Rules\MonthYearRule;
use Modules\RealEstate\Rules\PercentAmountGreaterThanAmountRule;
use Carbon\Carbon;
use Illuminate\Validation\Rule;


class RlstBuildingPolicyRequest extends FormRequest
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
            "building-policy" => 'required|array',
            "building-policy.*.building_id" => ['required','integer','exists:rlst_buildings,id,deleted_at,NULL'],
            "building-policy.*.year" => ['required','integer','gt:1500'],            
            "building-policy.*.month" => ['required','date_format:m', 'between:1,12'],
            "building-policy.*.policy_id" => ['required','integer','exists:rlst_policies,id', new UniqueBuildingDatePolicyRule($this->id)],
            "building-policy.*.amount" => ['sometimes','numeric','gt:0'],
            "building-policy.*.percent" => ['sometimes','numeric','between:0,100'],     
            "building-policy.*.percent_amount" => ['sometimes','numeric','gt:0', new PercentAmountGreaterThanAmountRule], 
            'building-policy.*.after_expenses'    => 'sometimes|in:yes,no',
            'building-policy.*.collected_rent_type' => 'required|in:accrued,actual',
            'building-policy.*.plus_extra_revenues' => 'sometimes|in:yes,no',
            'building-policy.*.company_pays_rest' => 'sometimes|in:yes,no',
            'building-policy.*.owner_pays_rest' => 'sometimes|in:yes,no',
        ];
    }

}
