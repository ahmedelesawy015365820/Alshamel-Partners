<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            "execution_date" => "nullable|date",
            "execution_end_date" => "nullable|date|after_or_equal:execution_date",
            "notification_date" => "nullable|date",
            "execution_duration" => "nullable|string",
            "contact_phone" => "nullable|string",
            "contact_person" => "nullable|string",
            "note" => "nullable|string",
            "task_title"    => "required|string",
            "department_id" => "required|exists:general_departments,id",
            "employee_id" => "nullable|exists:general_employees,id",
            "department_task_id" => "nullable|exists:general_depertment_tasks,id",
            "status_id" => "required|exists:general_statuses,id",
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s|before_or_equal:execution_end_date',
            "type" => "nullable|in:equipment,customer,general",
            "equipment_id" => "nullable|exists:general_equipments,id",
            "customer_id" => "nullable|exists:general_customers,id",
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            "supervisors" => "nullable|array",
            "supervisors.*" => "nullable|exists:general_employees,id",
            "attentions" => "nullable|array",
            "attentions.*" => "nullable|exists:general_employees,id",
            "location_id" => "nullable|exists:general_locations,id",
            "priority_id" => "required|exists:general_priorities,id",
            "is_closed" => "nullable|in:0,1",
            "task_requirement" => 'nullable|string'
        ];
    }
}
