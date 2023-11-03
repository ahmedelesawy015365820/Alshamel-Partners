<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:boards_rent_unit_statuses,name,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'name_e' => 'required|unique:boards_rent_unit_statuses,name_e,'. ($this->method() == 'PUT' ?  $this->id : ''),
            "status_id" => "required|exists:general_statuses,id",
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
