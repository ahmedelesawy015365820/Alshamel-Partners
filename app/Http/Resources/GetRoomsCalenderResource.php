<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class GetRoomsCalenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $date = Carbon::now();
        return [
            'id' => $this->id,
            'title' => $this->name,
            'start' => $this->Detail->date_from->format('Y-m-d H:i:s') ?? $date->toDateString(),
            'end' => $this->Detail->date_to->format('Y-m-d H:i:s') ?? $date->endOfMonth()->toDateString(),
            'className-unit' => $this->detail->bookingUnitStatus ? $this->detail->bookingUnitStatus->name : null,
            'className' => $this->detail->bookingUnitStatus ? $this->detail->bookingUnitStatus->color : null,
        ];



    }
}
