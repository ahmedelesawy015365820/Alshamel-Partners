<?php

namespace App\Http\Requests\DocumentCompanyModuleStatus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentCompanyModuleStatusRequest extends FormRequest
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
        $request =  request();

        return [
            'document_id' => [
                'sometimes',
                'exists:general_documents,id',
                Rule::unique('general_document_company_module_statuses')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('company_id', $request->company_id)->where('document_module_type_id',$request->document_module_type_id)->where('status_id',$request->status_id);
                }),
            ],
            'document_module_type_id' => [
                'sometimes',
                'exists:general_document_module_types,id',
                Rule::unique('general_document_company_module_statuses')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('company_id', $request->company_id)->where('document_id',$request->document_id)->where('status_id',$request->status_id);
                }),
            ],
            'status_id' => [
                'sometimes',
                'exists:general_statuses,id',
                Rule::unique('general_document_company_module_statuses')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('company_id', $request->company_id)->where('document_id',$request->document_id)->where('document_module_type_id',$request->document_module_type_id);
                }),

            ],
            'company_id' => 'nullable' ,
        ];
    }
}
