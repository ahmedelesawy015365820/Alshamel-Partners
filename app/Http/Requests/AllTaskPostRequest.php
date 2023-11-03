<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllTaskPostRequest extends FormRequest
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
            "customer_ids" => "nullable|array",
            "customer_ids.*" => "required_with:customer_ids|exists:general_customers,id",
            "status_ids" => "nullable|array",
            "status_ids.*" => "required_with:status_ids|exists:general_statuses,id",
            "department_ids" => "nullable|array",
            "department_ids.*" => "required_with:department_ids|exists:general_departments,id",
            "department_task_ids" => "nullable|array",
            "department_task_ids.*" => "required_with:department_task_ids|exists:general_depertment_tasks,id",
            "employee_ids" => "nullable|array",
            "employee_ids.*" => "required_with:employee_ids|exists:general_employees,id",
        ];
    }
}
