<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimeTablesDetailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'time_details' => 'required|array',
            'time_details.*.timetables_header_id' => 'required|exists:hr_timetables_header,id',
            'time_details.*.day_no' => 'required|in:1,2,3,4,5,6,7',
            'time_details.*.shift1_from' => 'nullable',
            'time_details.*.shift1_to' => 'nullable|after:time_details.*.shift1_from',
            'time_details.*.shift2_from' => 'nullable',
            'time_details.*.shift2_to' => 'nullable|after:time_details.*.shift2_from',
            'time_details.*.is_off' => 'required|in:0,1',
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
