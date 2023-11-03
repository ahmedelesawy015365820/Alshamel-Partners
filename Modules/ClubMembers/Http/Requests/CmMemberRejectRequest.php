<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmMemberRejectRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date',
            'branch_id' => "required|exists:general_branches,id",
            'document_id' => "required|exists:general_documents,id",
            'serial_id' => "nullable|exists:general_serials,id",
            'cm_member_id' => "required|exists:cm_members,id",
            'session_number' => "nullable|string",
            'note' => "nullable|string",
            'discharge_reson_id' => "required|exists:cm_discharge_reson,id",
            'entity' => 'nullable|string|max:20',
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
