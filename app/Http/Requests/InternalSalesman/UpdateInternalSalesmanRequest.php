<?php

namespace App\Http\Requests\InternalSalesman;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInternalSalesmanRequest extends FormRequest
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
            "employee_id"     => "nullable|exists:general_employees,id" ,
            "is_active"       => "nullable|in:active,inactive" ,
        ];
    }
}
