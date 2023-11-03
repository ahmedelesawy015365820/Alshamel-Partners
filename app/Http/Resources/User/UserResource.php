<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Employee\RelationEmployeeResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\Roles\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'employee_id' => $this->employee_id,
            'is_active' => $this->is_active,
            "email" => $this->email,
            "employee" => $this->employee,
            "role" => $this->roles,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
            "type" => $this->type
        ];
    }
}
