<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pre_in' => 'required|integer|max_digits:11',
            'post_in' => 'required|integer|max_digits:11',
            'absent_minutes' => 'required|integer|max_digits:11',
            'pre_out' => 'required|integer|max_digits:11',
            'post_out' => 'required|integer|max_digits:11',
            'max_out' => 'required|integer|max_digits:11',

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
