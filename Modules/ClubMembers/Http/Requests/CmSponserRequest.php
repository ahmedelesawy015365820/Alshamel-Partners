<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmSponserRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255|unique:cm_sponsers,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e' => 'sometimes|string|max:255|unique:cm_sponsers,name_e' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'parent_id' => 'nullable',
            "company_id" => 'nullable',
            'group_id' => 'nullable|exists:cm_sponsors_group,id',
            'cm_member_id' => 'nullable|exists:cm_members,id|unique:cm_sponsers,cm_member_id' . ($this->method() == 'PUT' ? ',' . $this->id : '')
        ];

    }

    public function messages()
    {
        return [
            'name.unique' => __('message.name already exists'),
            'name_e.unique' => __('message.name already exists'),
            'parent_id.exists' => __('message.parent not found'),
            'cm_member_id.unique' => __('العضو موجود بالفعل فى قائمة الرعاه'),
        ];
    }

}
