<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestTypeResource extends JsonResource
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
            'start_from' => $this->start_from,
            'end_date' => $this->end_date,
            'amount' => $this->amount,
            'from_hour' => $this->from_hour,
            'to_hour' => $this->to_hour,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'managers' => $this->employees

        ];
    }
}
