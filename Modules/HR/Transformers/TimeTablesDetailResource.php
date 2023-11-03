<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeTablesDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */

    public function toArray($request)
    {
        return[
            'timetables_header_id' => $this->timetables_header_id,
            'day_no' => $this->day_no,
            'shift1_from' => $this->shift1_from,
            'shift1_to' => $this->shift1_to,
            'is_off' => $this->is_off,
            'timetables_header' => $this->timetablesHeader,

        ];
    }
}
