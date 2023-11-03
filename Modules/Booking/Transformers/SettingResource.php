<?php

namespace Modules\Booking\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;


class SettingResource extends JsonResource
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
            'key' => $this->key,
            'extend_automatically' => $this->extend_automatically,
            'value' => Carbon::createFromTimeString($this->value)->format('H:i'), // Format the time as "09:00"

        ];
    }
}
