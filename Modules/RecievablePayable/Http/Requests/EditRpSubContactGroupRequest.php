<?php

namespace Modules\RecievablePayable\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditRpSubContactGroupRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'name' => ['required','string','max:255','unique:rp_sub_contact_groups,name,'.$this->rp_sub_contact_group],
            'name_e' => ['required','string','max:255','unique:rp_sub_contact_groups,name_e,'.$this->rp_sub_contact_group],
            'gl_acc_no'=>[],
            'rp_main_contact_group_id'=>[]
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
