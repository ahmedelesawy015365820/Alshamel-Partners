<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstReservationUnitRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "unit_code" => "required|exists:rlst_reservations,id,deleted_at,NULL",
            "company_id"=>'nullable',

        ];
    }


}
