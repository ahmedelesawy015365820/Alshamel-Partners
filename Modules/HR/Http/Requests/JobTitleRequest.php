<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobTitleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'nullable',
            'name' => 'required|string|max:100|unique:hr_job_title,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e' => 'required|string|max:100|unique:hr_job_title,name_e' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
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
