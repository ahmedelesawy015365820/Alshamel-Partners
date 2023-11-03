<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestManagerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [

            'approved_from_date' => 'required|date',
            'approved_to_date' => 'required|date',
            'approved_from_hour' => 'required|date_format:H:i',
            'approved_to_hour' => 'required|date_format:H:i',
            'approved_amount' => 'null|numeric',
            'approved_date' => 'required|date',
            'request_status_id' => 'required|exists:hr_statuses,id',
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
