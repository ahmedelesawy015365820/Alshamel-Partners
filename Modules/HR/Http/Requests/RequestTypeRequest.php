<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:hr_requests_types,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e' => 'required|string|max:100|unique:hr_requests_types,name_e' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'start_from' => 'nullable|in:0,1',
            'end_date' => 'nullable|in:0,1',
            'amount' => 'nullable|in:0,1',
            'from_hour' => 'nullable|in:0,1',
            'to_hour' => 'nullable|in:0,1',
            'managers' => 'nullable|array',
            'managers.*' => 'nullable|exists:general_employees,id',
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
