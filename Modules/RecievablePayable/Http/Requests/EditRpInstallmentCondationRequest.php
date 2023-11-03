<?php

namespace Modules\RecievablePayable\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditRpInstallmentCondationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required','string','max:255','unique:rp_installment_condations,name,'.$this->rp_installment_condation],
            'name_e' => ['required','string','max:255','unique:rp_installment_condations,name_e,'.$this->rp_installment_condation],
            'is_default'=>[]
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
