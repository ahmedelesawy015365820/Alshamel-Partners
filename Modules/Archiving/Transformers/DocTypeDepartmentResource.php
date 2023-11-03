<?php

namespace Modules\Archiving\Transformers;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Archiving\Entities\Department;
use Modules\Archiving\Entities\DocType;

class DocTypeDepartmentResource extends JsonResource
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
            'arch_doc_type_id' => $this->arch_doc_type_id,
            'arch_department_id' => $this->arch_department_id,
            'doc_type' => new DocTypeResource(DocType::find($this->arch_doc_type_id)),
            'department' => new DepartmentResource(Department::find($this->arch_department_id)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
