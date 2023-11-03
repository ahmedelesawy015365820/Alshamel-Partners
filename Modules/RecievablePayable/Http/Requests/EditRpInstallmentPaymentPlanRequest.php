<?php

namespace Modules\RecievablePayable\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditRpInstallmentPaymentPlanRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required','string','max:255','unique:rp_installment_payment_plans,name,'.$this->rp_installment_p_plan],
            'name_e' => ['required','string','max:255','unique:rp_installment_payment_plans,name_e,'.$this->rp_installment_p_plan],
            'is_default' => 'required',
            'is_active' => [],
            'description' => [],
            'description_e' => [],

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
