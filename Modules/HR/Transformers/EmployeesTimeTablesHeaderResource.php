<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeesTimeTablesHeaderResource extends JsonResource
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
            'id' => $this->id
            , 'timetables_header_id' => $this->timetables_header_id
            , 'start_from' => $this->start_from
            , 'timetablesHeader' => $this->timetablesHeader,
        ];
    }
}
