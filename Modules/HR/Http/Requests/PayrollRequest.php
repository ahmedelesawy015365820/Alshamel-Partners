<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employee_id' => 'required|exists:general_employees,id',
            'date' => 'required|unique:hr_payrolls,date',
            'basic_salary' => 'nullable',
            'work_days' => 'nullable',
            'work_value' => 'nullable',
            'benefits' => 'nullable',
            'deductions' => 'nullable',
            'net' => 'nullable',
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
