<?php

namespace App\Http\Requests\GeneralCustomer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeneralCustomerRequest extends FormRequest
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
        $request = request();

        return [

            'name' => [
                'nullable',
                Rule::unique('general_customers')->ignore($this->general_customer)->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                }),
            ],
            'name_e' => [
                'nullable',
                Rule::unique('general_customers')->ignore($this->general_customer)->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                }),
            ],

            'phone' => [
                'nullable',
                Rule::unique('general_customers')->ignore($this->general_customer)->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                }),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('general_customers')->ignore($this->general_customer)->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                }),
            ],

            'country_id' => 'nullable|exists:general_countries,id',
            'city_id' => 'nullable|exists:general_cities,id',
            'rp_code' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:255',
            'bank_account_id' => 'nullable|exists:general_bank_accounts,id',
            'contact_person' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:100',
            'national_id' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'passport_no' => 'nullable|string|max:20',
            'note' => 'nullable|string|max:100',
            'code_country' => 'nullable|string',
            'facebook' => 'nullable|string|url',
            'instagram' => 'nullable|string|url',
            'linkedin' => 'nullable|string|url',
            'snapchat' => 'nullable|string|url',
            'twitter' => 'nullable|string|url',
            'website' => 'nullable|string|url',
            'company_id' => 'nullable',
            'customer_main_category_id' => 'nullable',
            'customer_sub_category_id' => 'nullable',
            'is_supplier' => 'nullable|in:1,0',
            "employee_id" => "nullable|exists:general_employees,id",
            "salesman_id" => "nullable|exists:general_salesmen,id",
            "customer_group_id" => "nullable|exists:general_customer_groups,id",
            "sector_id" => "nullable|exists:general_sectors,id",
            "customer_source_id" => "nullable|exists:general_customer_sources,id",
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            'type' => 'nullable|in:1,0',
            'address' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:20',
            'tax_record' => 'nullable|string|max:20',
            'passport_expiry_date' => 'nullable|date',
            'visa_no' => 'nullable|string|max:20',

        ];

    }

}
