<?php

namespace Modules\Archiving\Transformers;

use Modules\Archiving\Entities\DocTypeField;
use Illuminate\Http\Resources\Json\JsonResource;

class AllDocTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'is_valid' => $this->is_valid,
            'parent_id' => new  DocRelationResource($this->parent),
            'doc_type_field' => DocTypeFieldResource::collection(DocTypeField::where('doc_type_id', $this->id)->get()),
            'sub_docs' => $this->children,
            "arch_department_id" => $this->arch_department_id,
        ];
    }
}
