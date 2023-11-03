<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LookUpRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:boards_rent_look_ups,name,' . ($this->method() == 'PUT' ? $this->id : ''),
            'name_e' => 'required|unique:boards_rent_look_ups,name_e,' . ($this->method() == 'PUT' ? $this->id : ''),
            "type" => "nullable|string",
            "parent_id" => "nullable|exists:boards_rent_look_ups,id",
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
