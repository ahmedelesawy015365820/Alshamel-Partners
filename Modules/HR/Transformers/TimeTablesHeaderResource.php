<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeTablesHeaderResource extends JsonResource
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
            'tt_monthly_hours' => $this->tt_monthly_hours,
            'timetable_types_id' => $this->timetable_types_id,
            'TimeTableType' => $this->TimeTableType,
            'timetablesDetails' => $this->timetablesDetails,
        ];
    }

}
