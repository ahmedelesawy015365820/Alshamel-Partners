<?php

namespace Modules\RecievablePayable\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRpInstallmentCondationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
//        dd($this->route());
        return [
            'name' => 'required|string|max:255|unique:rp_installment_condations,name',
            'name_e' => 'required|string|max:255|unique:rp_installment_condations,name_e',
            'is_default'=>[],
            "company_id"=>'nullable',
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
