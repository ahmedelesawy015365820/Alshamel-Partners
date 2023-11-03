<?php

namespace Modules\Booking\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UnitRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->id;

        return [
            'code' => "nullable|string|max:20|" . Rule::unique('booking_units', 'code')->whereNull('deleted_at')->ignore($id),
            'name' => "nullable|string|max:100|" . Rule::unique('booking_units', 'name')->whereNull('deleted_at')->ignore($id),
            'name_e' => "nullable|string|max:100|" . Rule::unique('booking_units', 'name_e')->whereNull('deleted_at')->ignore($id),

            'description' => "nullable|string|max:255",
            'description_e' => "nullable|string|max:255",

            'price' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'extra_guest_price' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'number_of_individuals' => 'nullable|numeric|min:0',
            'booking_floor_id' => 'nullable|exists:booking_floors,id',
            'unit_status_id' => 'nullable',

            'company_id' => 'nullable',
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
