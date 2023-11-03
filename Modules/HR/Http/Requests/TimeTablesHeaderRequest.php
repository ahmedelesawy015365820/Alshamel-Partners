<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimeTablesHeaderRequest extends FormRequest
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
                'name' => 'required|string|max:50|unique:hr_timetables_header,name'. ($this->method() == 'PUT' ? ',' . $this->id : ''),
                'name_e' => 'required|string|max:50|unique:hr_timetables_header,name_e'. ($this->method() == 'PUT' ? ',' . $this->id : ''),
                'timetable_types_id' => 'required|integer|exists:hr_timetable_types,id',
                'tt_monthly_hours' => 'required|integer|max_digits:3',
            ];
        } else {
            return [
                'name' => 'required|string|max:50|unique:hr_timetables_header,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
                'name_e' => 'required|string|max:50|unique:hr_timetables_header,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
                'timetable_types_id' => 'required|integer|exists:hr_timetable_types,id',
                'tt_monthly_hours' => 'required|integer|max_digits:3',

                'time_details' => 'required|array',
                'time_details.*.timetables_header_id' => 'required_with:time_details|exists:hr_timetables_header,id',
                'time_details.*.day_no' => 'required_with:time_details|in:1,2,3,4,5,6,7',
                'time_details.*.shift1_from' => 'nullable',
                'time_details.*.shift1_to' => 'nullable|after:time_details.*.shift1_from',
                'time_details.*.shift2_from' => 'nullable',
                'time_details.*.shift2_to' => 'nullable|after:time_details.*.shift2_from',
                'time_details.*.is_off' => 'required_with:time_details|in:0,1',
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
