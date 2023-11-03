<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmUpdateAcceptedMemberRequest extends FormRequest
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

            'status_id' => 'required|exists:general_statuses,id',
            'sponsor_id' => 'required|exists:cm_sponsers,id',
            'financial_status_id' => 'required|exists:cm_financial_status,id',
        ];
    }
}
