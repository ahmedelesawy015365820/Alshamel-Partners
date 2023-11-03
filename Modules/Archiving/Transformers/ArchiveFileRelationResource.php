<?php

namespace Modules\Archiving\Transformers;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveFileRelationResource extends JsonResource
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
            "data_type_value" => $this->data_type_value,
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "arch_department_id" => $this->arch_department_id,
        ];

    }
}
