<?php

namespace Modules\HR\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class RequestRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $from_date = !request('from_date') && request('to_date') ? '|after_or_equal:' .
        Carbon::parse(request()->request_datetime)->format("Y-m-d") : '';
        $both_date = request('from_date') && request('to_date') ? '|after_or_equal:from_date' : '';
        return [
            'employee_id' => 'nullable|exists:general_employees,id',
            'request_types_id' => 'nullable|exists:hr_requests_types,id',

            'request_datetime' => 'nullable',

            'from_date' => 'nullable|date|after_or_equal:' . Carbon::parse(request()->request_datetime)->format("Y-m-d"),
            'to_date' => 'nullable|date' . $from_date . $both_date,
            'remark' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric',
            'from_hour' => 'nullable',

            'approved_by' => 'nullable',

            'to_hour' => 'nullable' . request('from_hour') && request('to_hour') ? '|after:from_hour' : '',

            'request_status_id' => 'nullable|exists:hr_statuses,id',

            // "media" => "nullable|array",
            // "media.*" => ["nullable", new \App\Rules\MediaRule()],

            "media" => "nullable|array",
            // "media.*" => ["nullable|exists:media,id", new \App\Rules\MediaRule()],
            // 'old_media.*' => ['nullable|exists:media,id', new \App\Rules\MediaRule("Modules\HR\Entities\Request")],

            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            'old_media.*' => ['nullable', "exists:media,id", new \App\Rules\MediaRule("Modules\HR\Entities\Request")],

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
