<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstContractFilterRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'salesman_id' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'building_id' => 'nullable',
            'unit_id' => 'nullable',
            'wallet_id' => 'nullable',
            'owner_id' => 'nullable',
            'unit_value' => 'nullable',
            'unit_value_from' => 'nullable',
            'unit_value_to' => 'nullable',
            'properties' => 'nullable',
            'lat' => 'required_with:lng',
            'lng' => 'required_with:lat',
        ];
    }

}
