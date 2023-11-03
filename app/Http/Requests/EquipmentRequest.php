<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'name_e' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:general_equipments,id',
            "location_id" => "nullable|exists:general_locations,id",
            "periodic_maintenance_id" => "nullable|exists:general_periodic_maintenances,id",
            "company_id"=>'nullable',
        ];
    }
}
