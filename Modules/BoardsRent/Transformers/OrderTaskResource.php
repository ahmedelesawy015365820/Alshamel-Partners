<?php

namespace Modules\BoardsRent\Transformers;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Archiving\Transformers\DepartmentResource;
use Modules\BoardsRent\Transformers\CustomerResource;

class OrderTaskResource extends JsonResource
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
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'note' => $this->note,
            "must_completed" => $this->must_completed,
            'order' => new OrderResource($this->order),
            "task" => new TaskResource($this->task),
            "customer" => new CustomerResource($this->customer),
            "user" => new UserResource($this->user),
            'department' => new DepartmentResource($this->department),
            "created_at" => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
