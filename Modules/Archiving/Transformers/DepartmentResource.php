<?php

namespace Modules\Archiving\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Archiving\Entities\DocumentField;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //        $key_val = DocumentField::query()->where('id', $this->key_value)->first();
        $id = $this->id;
        $docItems = $this->arch_documents()->get()->map(function ($item) use ($id) {
            $item->arch_department_id = $id;
            return $item;
        });
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'parent_id' => $this->parent_id,
            'arch_department' => new DepartmentRelationResource($this->parent),
            'children' => DepartmentResource::collection($this->children),
            'arch_documents' => DocTypeResource::collection($docItems),
            'is_active' => $this->is_active,
            'is_key' => (bool) $this->is_key,
            //            'key_value' => new DocumentFieldResource($key_val),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            //            "archive_file"=>new ArchiveFileResource($this->archiveFile)
        ];
    }
}
