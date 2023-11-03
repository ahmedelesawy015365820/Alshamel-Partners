<?php

namespace App\Http\Resources\User;

use App\Http\Resources\PermissionLoginResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'is_active' => $this->is_active,
            "email" => $this->email,
            "permissions" => PermissionLoginResource::collection(($this->getAllPermissions())),
            "type" => $this->type,
            "employee_id" => $this->employee_id,
            'employee' => $this->employee,
            'company_id' => $this->company_id,
        ];


    }
}
