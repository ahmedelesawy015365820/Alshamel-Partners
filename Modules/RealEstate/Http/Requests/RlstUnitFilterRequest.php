<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstUnitFilterRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'building_id' => 'nullable',
            'unit_ty' => 'nullable',
            'unit_area' => 'nullable',
            'rooms' => 'nullable',
            'path' => 'nullable',
            'properties' => 'nullable',
            'wallet_id' => 'nullable',
            'owner_id' => 'nullable',
            'country_id' => 'nullable',
            'city_id' => 'nullable',
            'governorate_id' => 'nullable',

        ];
    }

}

