<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmMemberSettingRequest extends FormRequest
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
            'date' => 'sometimes|date',
            'member_type_id' => 'nullable|exists:cm_members_types,id',
            'financial_status_id' => 'nullable|exists:cm_financial_status,id',
            'general_status_id' => 'nullable|exists:general_statuses,id',
           
        ];
    }

}

