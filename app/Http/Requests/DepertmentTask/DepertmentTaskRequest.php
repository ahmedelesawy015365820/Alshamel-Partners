<?php

namespace App\Http\Requests\DepertmentTask;

use Illuminate\Foundation\Http\FormRequest;

class DepertmentTaskRequest extends FormRequest
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
            'name' => 'required|unique:general_depertment_tasks,name,' . ($this->method() == 'PUT' ? $this->id : ''),
            'name_e' => 'required|unique:general_depertment_tasks,name_e,' . ($this->method() == 'PUT' ? $this->id : ''),
            "description" => "required|string",
            "description_e" => "required|string",
            "department_id" => "required|exists:general_departments,id",
            "estimate_task_duration" => "nullable|array"
        ];
    }
}
