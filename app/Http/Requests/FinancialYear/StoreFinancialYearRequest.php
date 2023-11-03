<?php

namespace App\Http\Requests\FinancialYear;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinancialYearRequest extends FormRequest
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
        return [
            'name' => 'nullable|string|max:255|unique:general_financial_years,name',
            'name_e' => 'nullable|string|max:255|unique:general_financial_years,name_e',
            "start_date" => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:today',
            "end_date" => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:start_date',

        ];
    }



}
