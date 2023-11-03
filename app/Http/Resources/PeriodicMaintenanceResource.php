<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PeriodicMaintenanceResource extends JsonResource
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
            'date' => $this->date,
            'is_active' => $this->is_active,
            "restart_period_id" => $this->restart_period_id,
            'department_id'=> $this->department_id,
            'task_id'=> $this->task_id,
            "task" => $this->task,
            "department" => $this->department,
            "restart_period" => $this->restartPeriod,

        ];
    }
}
