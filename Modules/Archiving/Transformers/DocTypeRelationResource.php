<?php

namespace Modules\Archiving\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Archiving\Entities\DocTypeField;

class DocTypeRelationResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'is_valid' => $this->is_valid,
            // 'parent_id' => new DocRelationResource($this->parent),
            // 'children' => DocTypeResource::collection($this->children),
            'doc_type_field' => DocTypeFieldResource::collection(DocTypeField::where('doc_type_id', $this->id)->get()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "files_count"=>$this->archiveFiles->count()
        ];
    }
}
