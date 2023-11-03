<?php

namespace App\Http\Resources\RoleWorkflow;

use App\Http\Resources\Roles\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleWorkflowRelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'workflow_id' => $this->workflow_id,
            "workflow_name" => $this->workflow_name,
        ];
    }
}
