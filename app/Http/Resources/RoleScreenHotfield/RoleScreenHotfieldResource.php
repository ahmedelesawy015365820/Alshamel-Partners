<?php

namespace App\Http\Resources\RoleScreenHotfield;

use App\Http\Resources\Roles\RelationRoleResource;
use App\Http\Resources\Roles\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleScreenHotfieldResource extends JsonResource
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
            'role' => new RelationRoleResource($this->role),
            "workflow_id" => $this->workflow_id,
            "hotfield_id" => $this->hotfield_id
        ];
    }

}
