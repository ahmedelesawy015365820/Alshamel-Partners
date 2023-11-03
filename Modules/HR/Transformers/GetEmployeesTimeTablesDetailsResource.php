<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class GetEmployeesTimeTablesDetailsResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'department_id' => $this->department_id,
            'att_code' => $this->att_code,
            'branch_id' => $this->branch_id,
            'department' => $this->depertment,
            'branch' => $this->branch,

        ];
    }
}
