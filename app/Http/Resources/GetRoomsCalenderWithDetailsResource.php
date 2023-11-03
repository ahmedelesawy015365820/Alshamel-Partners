<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class GetRoomsCalenderWithDetailsResource extends JsonResource
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
            'id' => $this->unit->id,
            'title' => $this->unit->name,
            'start' => Carbon::parse($this->date_from)->format('Y-m-d h:m:s') ?? $date->toDateTimeString(),
            'end' => Carbon::parse($this->date_to)->format('Y-m-d h:m:s') ?? $date->endOfMonth()->toDateTimeString(),
            'className-unit' => $this->bookingUnitStatus ? $this->bookingUnitStatus->name : null,
            'className' => $this->bookingUnitStatus ? $this->bookingUnitStatus->color : null,
        ];


        // return $data = $this->documentHeaderDetails->map(function ($detail) {
        //     return [
        //         'id' => $this->id,
        //         'title' => $this->name,
        //         'start' => $detail->date_from->toDateTimeString() ?? null,
        //         'end' => $detail->date_to->toDateTimeString() ?? null,
        //         'className-unit' => $detail->unitStatus ? $detail->unitStatus->name : null,
        //         'className' => $detail->unitStatus ? $detail->unitStatus->color : null,
        //     ];
        // });
        // return $data;
    }
}
