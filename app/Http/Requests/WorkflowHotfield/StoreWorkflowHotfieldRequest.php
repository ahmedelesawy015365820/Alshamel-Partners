<?php

namespace App\Http\Requests\WorkflowHotfield;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkflowHotfieldRequest extends FormRequest
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
            "workflow_id"     => "nullable|numeric",
            "hotfield_id"   => "nullable|numeric",
            "company_id" => "nullable"
            // "workflow_name" => "required|string" ,
        ];
    }
}
