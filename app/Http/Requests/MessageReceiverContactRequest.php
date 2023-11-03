<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageReceiverContactRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'message_request_id' => 'required|exists:general_message_requests,id',
            'manager_employee_ids' => 'required|array',
            'manager_employee_ids.*' => 'exists:general_employees,id',
        ];
    }
}
