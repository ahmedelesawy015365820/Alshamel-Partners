<?php

namespace App\Http\Resources\DocumentCompanyModuleStatus;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentCompanyModuleStatusResource extends JsonResource
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
            "id" =>$this->id,
            "document_id" => $this->document_id,
            "document_module_type_id" => $this->document_module_type_id,
            "company_id" => $this->company_id,
            "status_id" => $this->status_id,
            "document" => $this->whenLoaded('document'),
            "status" => $this->whenLoaded('status'),
            "document_module_type" => $this->whenLoaded('documentModuleType'),
        ];
    }
}
