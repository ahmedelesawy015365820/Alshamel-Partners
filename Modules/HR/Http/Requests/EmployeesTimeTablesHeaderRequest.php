<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeesTimeTablesHeaderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->method() == 'POST') {
        return [
            'timetables_header_id' => [
                'required',
                'exists:hr_timetables_header,id',
                Rule::unique('hr_employees_timetables_header')->where(function ($query) {
                    return $query->where('start_from', request('start_from'));
                }),
            ],
            'start_from' => 'required|date',
        ];
    } else {
        return [
            'timetables_header_id' => [
                'required',
                'exists:hr_timetables_header,id',
                Rule::unique('hr_employees_timetables_header')->ignore($this->id)->where(function ($query) {
                    return $query->where('start_from', request('start_from'));
                }),
            ],
            'start_from' => 'required|date',

            'employee_time_details' => 'required|array',
            'employee_time_details.*' => 'required|exists:general_employees,id',
        ];
    }
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
