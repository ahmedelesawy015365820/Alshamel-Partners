<?php

namespace App\Http\Resources;

use App\Http\Resources\DepertmentTask\RelationDepertmentTaskResource;
use App\Http\Resources\Depertment\RelationDepertmentResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Employee\RelationEmployeeResource;
use App\Http\Resources\Equipment\RelationEquipmentResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\GeneralCustomer\RelationGeneralCustomerResource;
use App\Http\Resources\Location\RelationLocationResource;
use App\Http\Resources\Priority\RelationPriorityResource;
use App\Http\Resources\Status\RelationStatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $department_task_name = $this->departmentTask->name ?? null;
        $customer_name = $this->customer->name ?? null;
        $employee_name = $this->employee->name ?? null;
        $equipment_name = $this->equipment->name ?? null;
        $title =
        $this->task_title . '|' .
            $customer_name .
            $equipment_name . '|' .
            $employee_name . '|' .
            $department_task_name;

        return [
            'id' => $this->id,
            'title' => $title,
            'start' => $this->execution_date,
            'end' => $this->execution_end_date,
            'className' => $this->status->color,
            'execution_date' => $this->execution_date,
            'execution_end_date' => $this->execution_end_date,
            "notification_date" => $this->notification_date,
            "execution_duration" => $this->execution_duration,
            "contact_phone" => $this->contact_phone,
            "contact_person" => $this->contact_person,
            "note" => $this->note,
            "type" => $this->type,
            "equipment" => new RelationEquipmentResource($this->equipment),
            "task_title" => $this->task_title,
            'department_id' => $this->department_id,
            'employee_id' => $this->employee_id,
            "customer_id" => $this->customer_id,
            'department_task_id' => $this->department_task_id,
            "status_id" => $this->status_id,
            "start_time" => $this->start_time,
            "end_time" => $this->end_time,
            "department_task" => new RelationDepertmentTaskResource($this->departmentTask),
            "department" => new RelationDepertmentResource($this->department),
            "employee" => new RelationEmployeeResource($this->employee),
            "customer" => new RelationGeneralCustomerResource($this->customer),
            "status" => new RelationStatusResource($this->status),
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
            "owners" => $this->owners,
            "supervisors" => EmployeeResource::collection($this->supervisors),
            'attentions' => EmployeeResource::collection($this->attentions),
            "notifications" => $this->notifications,
            'location_id' => $this->location_id,
            'location' => new RelationLocationResource($this->location),
            'priority_id' => $this->priority_id,
            'priority' => new RelationPriorityResource($this->priority),
            'task_requirement' => $this->task_requirement,
            'is_closed' => $this->is_closed,
            'admin_note' => $this->admin_note,
        ];
    }
}
