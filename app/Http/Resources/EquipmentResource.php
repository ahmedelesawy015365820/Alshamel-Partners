<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            "location_id" => $this->location_id,
            "periodic_maintenance_id" => $this->periodic_maintenance_id,
            'parent' => $this->parent,
            "location" => $this->location,
            "periodic_maintenance" => $this->periodicMaintenance,

        ];
    }
}
