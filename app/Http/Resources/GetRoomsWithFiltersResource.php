<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class GetRoomsWithFiltersResource extends JsonResource
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

        $data = $this->documentHeaderDetails->map(function ($detail) use($date) {
            return [
                'id' => $this->id,
                'title' => $this->name,
                'start' => $detail ? $detail->date_from : $date->toDateString(),
                'end' => $detail ? $detail->date_to : $date->endOfMonth()->toDateString(),
                'className-unit' => $detail->unitStatus ? $detail->unitStatus->name : null,
                'className' => $detail->unitStatus ? $detail->unitStatus->color : null,
            ];
        });
        return $data;

    }
}
