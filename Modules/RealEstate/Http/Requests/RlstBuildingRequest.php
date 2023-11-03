<?php

namespace Modules\RealEstate\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;


class RlstBuildingRequest extends FormRequest
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
            'name'              => 'required|string|max:100',
            'name_e'            => 'required|string|max:100',
            'description'       => "sometimes|string",
            'description_e'     => "sometimes|string",
            'land_area'         => "sometimes|numeric|gt:0",
            'building_area'     => 'sometimes|numeric|lt:land_area',
            'construction_year' => ['sometimes','integer','between:1800,' . Carbon::now()->format('Y')],
            'country_id'        => "required|exists:general_countries,id,deleted_at,NULL",
            "governorrate_id"   => "required|exists:general_governorates,id,deleted_at,NULL",
            'city_id'           => "required|exists:general_cities,id",
            'avenue_id'         => "required|exists:general_avenues,id,deleted_at,NULL",
            'street_id'         => "required|exists:general_streets,id,deleted_at,NULL",
            'lng'               => "numeric|required_with:lat",
            'lat'               => "numeric|required_with:lng",
            'properties'        => "sometimes|array",
            'attachments'       => "sometimes|array",
            'module'            => "sometimes",
            "media"             => "nullable|array",
            "video_link"        =>"nullable|url|active_url|https" ,
            "media.*"           => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            "company_id"        => 'nullable',
            "building_type_id"  => "required|exists:rlst_building_types,id",
            "company_ownership" => "sometimes|in:0,1",
            "floors_number"     => "required|integer|gt:0",
            "vaults_number"     => "required|integer|min:0",
            "ground_floors_number" => "required|integer|min:0",
            "mediums_number"    => "required|integer|min:0",
            "elevators_number"  => "required|integer|min:0",
            "electricity_meters_number" => "required|integer|min:0",
            "water_meters_number" => "required|integer|min:0",
            "gas_meters_number" => "required|integer|min:0",
            "central_air_conditioning" => "sometimes|in:0,1",
            "buying_price" => "required|numeric|gt:0",
            "buying_date" => "required|date|before:today",
            "middleman_cost" => "sometimes|numeric|min:0",
            "registration_cost" => "sometimes|numeric|min:0",
            "building_currency_id" => "required|exists:general_currencies,id",
            "accrued_revenues_account_id" => "sometimes|exists:general_accounts,id",
            "advance_revenues_account_id" => "sometimes|exists:general_accounts,id",
            "revenues_account_id" => "sometimes|exists:general_accounts,id",
            "discounts_account_id" => "sometimes|exists:general_accounts,id",
            "cash_account_id" => "sometimes|exists:general_accounts,id",
            "knet_account_id" => "sometimes|exists:general_accounts,id",
            "insurance_account_id" => "sometimes|exists:general_accounts,id",
            "main_cost_center_id" => "required|exists:general_main_cost_centers,id",
            "financial_period" => "sometimes|in:monthly,yearly",

        ];
    }


}
