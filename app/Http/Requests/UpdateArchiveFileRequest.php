<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArchiveFileRequest extends FormRequest
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
            "arch_doc_type_id" => "nullable|exists:arch_doc_types,id",
            "data_type_value" => "nullable",
            // "media" => "nullable|array",
            // "media.*" => ["exists:media,id", new \App\Rules\MediaRule()],
            // 'old_media.*' => ['exists:media,id', new \App\Rules\MediaRule("App\Models\Country")],
        ];
    }
}
