<?php

namespace App\Http\Resources\WorkflowHotfield;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowHotfieldResource extends JsonResource
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
            "id" => $this->id,
            "workflow_id" => $this->workflow_id,
            "workflow_name" => $this->workflow_name,
            "hotfield_id" => $this->hotfield_id,
        ];
    }

}
