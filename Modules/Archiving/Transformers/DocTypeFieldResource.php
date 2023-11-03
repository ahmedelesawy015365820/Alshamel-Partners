<?php

namespace Modules\Archiving\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DocTypeFieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'doc_type_id' => new DocRelationResource($this->archDocType),
            'doc_field_id' => new DocumentFieldRelationResource($this->archField),
            'doc_field' => $this->doc_field_id,
            'field_order' => $this->field_order,
            'is_required' => $this->is_required,
            'field_characters' => $this->field_characters,
            "parent_id" => $this->parent_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
