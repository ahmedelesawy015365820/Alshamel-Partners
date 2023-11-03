<?php

namespace Modules\GL\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CreateGlCoaRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'acc_no' => 'required',
            'name' => 'required|unique:gl_coas,name',
            'name_e' => 'required|unique:gl_coas,name_e',
            'parent_no' => 'required',
            'accounttype_id' => 'required',
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
