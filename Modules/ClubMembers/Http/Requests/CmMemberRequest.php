<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmMemberRequest extends FormRequest
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
            'first_name'          => 'nullable|string|max:255',
            'second_name'         => 'nullable|string|max:255',
            'third_name'          => 'nullable|string|max:255',
            'last_name'           => 'nullable|string|max:255',
            'family_name'         => 'nullable|string|max:255',
            'birth_date'          => 'nullable|date',
            'national_id'         => ['nullable', 'string', new \App\Rules\NationalIdRule()],
            'nationality_class'   => 'nullable|string|max:255',
            'phone_code'          => 'nullable|string|max:255',
            'home_phone'          => 'nullable|string|max:255',
            'work_phone'          => 'nullable|string|max:255',
            'home_address'        => 'nullable|string|max:255',
            'work_address'        => 'nullable|string|max:255',
            'membership_date'     => 'nullable|date',
            'membership_number'   => 'nullable',
            'job'                 => 'nullable|string|max:255',
            'degree'              => 'nullable|string|max:255',
            'acceptance'          => 'nullable|in:0,1,2',
            'session_date'        => 'nullable|date',
            'session_number'      => 'nullable|max:255',
            'applying_date'       => 'nullable|date',
//            'applying_number'     => 'nullable|unique:cm_members,applying_number' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'applying_number'     => 'nullable',
//            'sponsor_id'          => 'nullable|exists:cm_sponsers,id',
            'sponsor_id'          => 'nullable',
            'auto_member_type_id' => 'nullable|exists:cm_members_types,id',
            'financial_status_id' => 'nullable|exists:cm_financial_status,id',
            'member_kind_id'      => 'nullable|exists:cm_members_types,id',
            'member_status_id'           => 'nullable|exists:cm_member_status,id',
            'notes'               => 'nullable|string|max:255',
            'gender'              => 'nullable|in:0,1',
            'financial_year_id'   => 'nullable|exists:general_financial_years,id',
            "company_id"          =>'nullable',
        ];
    }
}
