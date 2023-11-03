<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomsByFloorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if (count($this->documentHeaderDetails) > 0) {

            $data = $this->documentHeaderDetails->map(function ($detail) {
                return [
                    'id' => $this->id,
                    'title' => $this->name,
                    'booking_floor_id' => $this->booking_floor_id,
                    'start' => Carbon::parse($detail->date_from)->toDateTimeString(),
                    'end' =>  Carbon::parse($detail->date_to)->toDateTimeString(),
                    'available' => $detail->bookingUnitStatus ? 0 : 1,
                    'className-unit' => $detail->bookingUnitStatus ? $detail->bookingUnitStatus->name : null,
                    'className' => $detail->bookingUnitStatus ? $detail->bookingUnitStatus->color : null,
                ];
            });
            return $data;
        }
        else {
            $date = Carbon::now();

            return [
                'id' => $this->id,
                'title' => $this->name,
                'booking_floor_id' => $this->booking_floor_id,
                'start' => $date->toDateString(),
                'end' => $date->endOfMonth()->toDateString(),
                'available' => 1,
                'className-unit' => 'فارغة',
                'className' => 'bg-success text-white',
            ];
        }

    }
}
