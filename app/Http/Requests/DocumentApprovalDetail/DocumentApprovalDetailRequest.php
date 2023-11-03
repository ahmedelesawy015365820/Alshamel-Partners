<?php

namespace App\Http\Requests\DocumentApprovalDetail;

use Illuminate\Foundation\Http\FormRequest;

class DocumentApprovalDetailRequest extends FormRequest
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
            "document_serial_number" => "required|string",
            "notes" => "required|string",
            "decision_date" => "required|date",
            'approval_time' => 'required|date_format:H:i:s',
            "employee_id" => "required|exists:general_employees,id",
            "decision_id" => "required|exists:general_document_statuses,id",
            "company_id"=>'nullable',
        ];
    }
}
