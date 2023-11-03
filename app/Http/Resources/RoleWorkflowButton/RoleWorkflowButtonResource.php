<?php

namespace App\Http\Resources\RoleWorkflowButton;

use App\Http\Resources\Roles\RelationRoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleWorkflowButtonResource extends JsonResource
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
            "role" => new RelationRoleResource($this->role),
            "workflow_id" => $this->workflow_id,
            "button_id" => $this->button_id,
        ];
    }
}
