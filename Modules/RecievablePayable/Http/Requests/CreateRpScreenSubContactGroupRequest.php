<?php

namespace Modules\RecievablePayable\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CreateRpScreenSubContactGroupRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sub_contract_group_id' => [],
            'screen_id' => [],
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
