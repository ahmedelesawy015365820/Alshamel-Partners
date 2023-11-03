<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeesTimeTablesDetailResource extends JsonResource
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
            'employees_timetables_header_id' => $this->employees_timetables_header_id,
            'employee_id' => $this->employee_id,
            'employeeTimetablesHeader' => $this->employeeTimetablesHeader,
            'employee' => $this->employee,
        ];
    }
}
