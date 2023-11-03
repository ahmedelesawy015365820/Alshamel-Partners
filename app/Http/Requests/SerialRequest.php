<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SerialRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request =  request();
        return [
            'company_id' => 'nullable',
            'restart_period_id' => "nullable|exists:general_restart_period,id",
            'start_no'    => 'nullable',
            'perfix'      => 'nullable',
            'suffix'      => 'nullable',
            'document_id' => [
                'nullable',
                Rule::unique('general_serials')->ignore($this->serial)->where(function ($query) use($request) {
                    return $query->where('branch_id', $request->branch_id);
                }),
            ],
            'branch_id' => [
                'nullable',
                Rule::unique('general_serials')->ignore($this->serial)->where(function ($query) use($request) {
                    return $query->where('document_id', $request->document_id);
                }),
            ],
            "name" => "nullable|max:151|string",
            "name_e" => "nullable|max:151|string",
            "gender" => "nullable",
        ];
    }
}
