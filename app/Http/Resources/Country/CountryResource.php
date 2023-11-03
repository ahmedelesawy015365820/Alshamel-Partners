<?php

namespace App\Http\Resources\Country;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'long_name' => $this->long_name,
            'long_name_e' => $this->long_name_e,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            "short_code" => $this->short_code,
            "phone_key" => $this->phone_key,
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
            'national_id_length' => $this->national_id_length,
        ];
    }
}
