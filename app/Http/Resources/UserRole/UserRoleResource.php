<?php

namespace App\Http\Resources\UserRole;

use App\Http\Resources\Roles\RelationRoleResource;
use App\Http\Resources\User\RelationUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRoleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'role_id' => $this->role_id,
            "role" => new RelationRoleResource($this->role),
            "user" => new RelationUserResource($this->user),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
