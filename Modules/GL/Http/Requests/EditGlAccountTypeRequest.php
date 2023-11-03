<?php

namespace Modules\GL\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class EditGlAccountTypeRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:gl_account_types,name,' . $this->id,
            'name_e' => 'required|unique:gl_account_types,name_e,'. $this->id,
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
