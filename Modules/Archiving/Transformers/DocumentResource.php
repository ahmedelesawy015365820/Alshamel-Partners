<?php

namespace Modules\Archiving\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            'arch_doc_type' => new DocRelationResource($this->archDocType),
            'doc_status' => new DocStatusRelationResource($this->docStatus),
            // "arc_department" => DepartmentResource::collection($this->documents),
            'children'=>$this->children,
            'doc_description' => $this->doc_description,
            'url_reference' => $this->url_reference,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
