<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCmTypePermissionRequest extends FormRequest
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
            'cm_members_type_id' => 'required|exists:cm_members_types,id',
            'cm_permissions_id' => 'required|exists:cm_members_permissions,id',
            'cm_financial_status_id' => 'nullable|exists:cm_financial_status,id',
            'membership_period' => 'nullable|numeric',
            'allowed_subscription_date' => 'nullable|numeric',
        ];
    }

}
