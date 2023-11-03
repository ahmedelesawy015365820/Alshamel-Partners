<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeesTimeTablesDetailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employee_time_details' => 'required|array',
            'employees_timetables_header_id' => 'required|exists:hr_employees_timetables_header,id',
            'employee_time_details.*' => 'required|exists:general_employees,id',

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
