<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FromAdminDocumentRequest extends FormRequest
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
            "documents"              => "required|array",
            'documents.*id'          => 'nullable',
            'documents.*.name'       => 'required',
            'documents.*.name_e'     => 'required',
            'documents.*.is_admin'   => 'nullable|integer',
            'documents.*.is_default' => 'nullable|integer',
            'documents.*.company_id'  => 'nullable|integer',
            'documents.*.attributes'  => 'nullable',
            'company_id'  => 'nullable|integer',
            'document_relateds.*' => "nullable|integer",
        ];
    }
}
