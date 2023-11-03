<?php

namespace Modules\Archiving\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Archiving\Entities\ArchiveFile;
use Modules\Archiving\Entities\DocType;
use Modules\Archiving\Entities\DocTypeField;

class DocTypeResource extends JsonResource
{
    protected $departmentId;

    public function setDepartmentId($value)
    {
        $this->departmentId = $value;
        return $this;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $arch_file  = null;
        $key        = $this->key;
        $subIds     = [];
        $archFiles = ArchiveFile::get();
        foreach ($archFiles as $file) {
            $docType = DocType::find($file->arch_doc_type_id);
            if ($docType) {
                if ($docType->parent_id == $this->id && $file->arch_department_id == $this->arch_department_id) {
                    $keyField = null;
                    foreach ($file->data_type_value as $field) {
                        if ($field->name_e == $key->name_e) {
                            $arch_file = $file;
                            $keyField = $field;
                            break;
                        }
                    }
                    if ($keyField && !in_array($keyField->value, $subIds)) {
                        $subIds[] = $keyField->value;
                    }
                }
            }
        }
        $key["children"] = collect($subIds)->map(function ($id) use ($arch_file) {
            return [
                "name" => $id, "name_e" => $id, "archive_file" => $arch_file, "parent_doc_type_children" => $this->children,
                "parent_doc_id"=>$this->id,

            ];
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'is_valid' => $this->is_valid,
            'parent_id' => new DocRelationResource($this->parent),
            'sub_docs' => DocTypeResource::collection($this->children),
            'doc_type_field' => DocTypeFieldResource::collection(DocTypeField::where('doc_type_id', $this->id)->get()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "files_count" => $this->archiveFiles->count(),
            "arch_department_id" => $this->arch_department_id,
            'key' => [$key],
        ];
    }
}
