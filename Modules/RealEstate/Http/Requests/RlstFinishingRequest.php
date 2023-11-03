<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstFinishingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
         'name' => 'required|string|max:100|unique:rlst_finishings,name,deleted_at,NULL',
         'name_e' => 'required|string|max:100|unique:rlst_finishings,name_e,deleted_at,NULL',
         'company_id' =>'nullable',

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
