<?php

namespace App\Http\Requests;

use App\Http\Resources\TaskResource;
use Illuminate\Foundation\Http\FormRequest;

class PeriodicMaintenanceRequest extends FormRequest
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
            'name' => "required|max:255",
            'name_e' => "required|max:255",
            'task_id' => "required|exists:general_tasks,id",
            'department_id' => "required|exists:general_departments,id",
            "restart_period_id" => "required|exists:general_restart_period,id",
            'is_active' => "nullable|in:0,1",
            "company_id"=>'nullable',
        ];
    }
}
