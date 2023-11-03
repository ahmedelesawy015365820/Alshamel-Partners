<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnlineRequestRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_type_id' => 'nullable|exists:hr_requests_types,id',
            'employee_id' => 'nullable|exists:general_employees,id',
            'start_from' => 'nullable|date',
            'end_date' => 'nullable|date',

            'descriptions' => 'nullable|string|max:255',
            'amount' => 'nullable',
            'from_hour' => 'nullable',
            'to_hour' => 'nullable',

            'status_id' => 'nullable|exists:hr_statuses,id',
            'approved_date' => 'nullable|date',

            'approved_from_date' => 'nullable|date',
            'approved_to_date' => 'nullable|date',

            'approved_from_hour' => 'nullable',
            'approved_to_hour' => 'nullable',
            'approved_amount' => 'nullable',
            'date' => 'nullable',
            'admin_comment' => 'nullable|string|max:255',
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
