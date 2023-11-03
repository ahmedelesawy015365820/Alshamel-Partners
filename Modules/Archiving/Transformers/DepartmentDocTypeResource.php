<?php

namespace Modules\Archiving\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentDocTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        $id=$this->id;
        $docItems=$this->arch_documents()->get()->map(function($item) use($id){
            $item->arch_department_id=$id;
            return $item;
        });
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'parent_id' => $this->parent_id,
            'is_active' => $this->is_active,
            'arch_documents' => NewDocTypeResource::collection($docItems),
        ];
    }
}
