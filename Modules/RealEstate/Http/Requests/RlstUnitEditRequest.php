<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstUnitEditRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => "nullable|string|max:20|unique:rlst_units,code,deleted_at,NULL",
            'name' => "nullable|string|max:100",
            'name_e' => "nullable|string|max:100",
            'description' => "nullable|string",
            'description_e' => "nullable|string",
            'unit_ty' => "nullable|integer",
            'unit_status_id' => "nullable|integer|exists:rlst_unit_statuses,id,deleted_at,NULL",
            'unit_area' => "nullable|numeric",
            'unit_net_area' => "nullable|numeric",
            'building_id' => "nullable|integer|exists:rlst_buildings,id,deleted_at,NULL",
            'rooms' => "nullable|integer",
            'path' => "nullable|integer",
            'view' => "nullable|integer",
            'floor' => "nullable|integer",
            'finishing' => "nullable|integer",
            'properties' => "nullable|array",
            'attachments' => "nullable|array",
            'module' => "nullable|string",
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            'old_media.*' => ['nullable', "exists:media,id", new \App\Rules\MediaRule("Modules\RealEstate\Entities\RlstUnit")],
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
