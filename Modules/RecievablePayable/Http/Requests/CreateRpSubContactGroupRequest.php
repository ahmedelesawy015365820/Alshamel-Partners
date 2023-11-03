<?php

namespace Modules\RecievablePayable\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CreateRpSubContactGroupRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:rp_sub_contact_groups,name',
            'name_e' => 'required|string|max:255|unique:rp_sub_contact_groups,name_e',
            'gl_acc_no'=>[],
            'rp_main_contact_group_id'=>[],
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
