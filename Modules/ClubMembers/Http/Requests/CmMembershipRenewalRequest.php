<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmMembershipRenewalRequest extends FormRequest
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

        // required|date_format:m-d|unique:customers,email_address

        return [
            'memberships_renewals' => 'required|array',
            'memberships_renewals.*.from' => 'required|unique:cm_memberships_renewals,from',
            'memberships_renewals.*.to' => 'required|after:from|unique:cm_memberships_renewals,to',
            'memberships_renewals.*.membership_availability' => 'required|in:0,1',
            'memberships_renewals.*.membership_cost' => 'required|regex:/^\d+(\.\d{1,2})?$/',

            'memberships_renewals.*.renewal_availability' => 'required|in:0,1',
            'memberships_renewals.*.renewal_cost' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            "company_id"=>'nullable',
        ];

    }

}
