<?php

namespace App\Http\Requests\FinancialYear;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FinancialYearRequest extends FormRequest
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

            // 'name' => 'nullable|string|max:255|unique:general_financial_years,name,' . ($this->method() == 'PUT' ? $this->id : ''),
            // 'name_e' => 'nullable|string|max:255|unique:general_financial_years,name_e,' . ($this->method() == 'PUT' ? $this->id : ''),

            // "start_date" => 'nullable|date_format:Y-m-d|unique:general_financial_years,start_date,' . ($this->method() == 'PUT' ? $this->id : ''),
            // "end_date" => 'nullable|date_format:Y-m-d|after_or_equal:start_date|unique:general_financial_years,end_date,' . ($this->method() == 'PUT' ? $this->id : ''),

            // "year" => 'nullable|integer|unique:general_financial_years,year,' . ($this->method() == 'PUT' ? $this->id : ''),
            // "dye_date" => 'nullable|date',
            // "is_active" => 'nullable',

            'name' => [
                'sometimes',
                Rule::unique('general_financial_years')->ignore($this->id)->where(function ($query) {
                    return $query->where('deleted_at', null);
                }),
            ],
            'name_e' => [
                'sometimes',
                Rule::unique('general_financial_years')->ignore($this->id)->where(function ($query) {
                    return $query->where('deleted_at', null);
                }),
            ],
            'start_date' => [
                'nullable',
                'date_format:Y-m-d',
                Rule::unique('general_financial_years')->ignore($this->id)->where(function ($query) {
                    return $query->where('deleted_at', null);
                }),
            ],
            'end_date' => [
                'nullable',
                'date_format:Y-m-d',
                Rule::unique('general_financial_years')->ignore($this->id)->where(function ($query) {
                    return $query->where('deleted_at', null);
                }),
            ],
            'year' => [
                'nullable',
                'integer',
                Rule::unique('general_financial_years')->ignore($this->id)->where(function ($query) {
                    return $query->where('deleted_at', null);
                }),
            ],
            'due_date' => [
                'nullable',
                'date',
                Rule::unique('general_financial_years')->ignore($this->id)->where(function ($query) {
                    return $query->where('deleted_at', null);
                }),
            ],

          'is_active' => ['nullable'],


        ];
    }

}
