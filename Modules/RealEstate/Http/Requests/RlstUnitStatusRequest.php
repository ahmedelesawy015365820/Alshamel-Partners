<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstUnitStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:rlst_unit_statuses,name,deleted_at,NULL',
            'name_e' => 'required|string|max:100|unique:rlst_unit_statuses,name_e,deleted_at,NULL',
            "is_active" => "nullable|in:active,inactive",
            "is_default" => "nullable|in:0,1",
            "company_id"=>'nullable',
        ];
    }


}
