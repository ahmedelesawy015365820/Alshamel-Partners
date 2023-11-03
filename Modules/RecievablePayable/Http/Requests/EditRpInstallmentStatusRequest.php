<?php

namespace Modules\RecievablePayable\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditRpInstallmentStatusRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {

        return [
            'name' => ['required','string','max:255','unique:rp_installment_statuses,name,'.$this->rp_installment_status],
            'name_e' => ['required','string','max:255','unique:rp_installment_statuses,name_e,'.$this->rp_installment_status],
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
