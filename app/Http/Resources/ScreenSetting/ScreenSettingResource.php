<?php

namespace App\Http\Resources\ScreenSetting;

use Illuminate\Http\Resources\Json\JsonResource;

class ScreenSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "screen_id" => $this->screen_id,
            "data_json" => json_decode($this->data_json),
        ];
    }
}
