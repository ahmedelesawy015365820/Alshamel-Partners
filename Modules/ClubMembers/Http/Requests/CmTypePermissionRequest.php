<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmTypePermissionRequest extends FormRequest
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
        if ($this->method() == 'PUT') {
            return [
                'cm_members_type_id' => 'required|exists:cm_members_types,id',
                'cm_permissions_id' => 'required|exists:cm_members_permissions,id',
                'cm_financial_status_id' => 'required|exists:cm_financial_status,id',
                'membership_period' => 'required|numeric',
                'allowed_subscription_date' => 'required',
                'allowed_vote_date' => 'nullable',
                "company_id" => 'nullable',
            ];
        }

        return [
            'memberships_renewals' => 'required|array',
            'memberships_renewals.*.cm_members_type_id' => 'required|exists:cm_members_types,id',
            'memberships_renewals.*.cm_permissions_id' => 'required|exists:cm_members_permissions,id',
            'memberships_renewals.*.cm_financial_status_id' => 'required|exists:cm_financial_status,id',
            'memberships_renewals.*.membership_period' => 'required|numeric',
            'memberships_renewals.*.allowed_subscription_date' => 'required',
            'memberships_renewals.*.allowed_vote_date' => 'nullable',
            "memberships_renewals.*.company_id" => 'nullable',
        ];
    }

}
