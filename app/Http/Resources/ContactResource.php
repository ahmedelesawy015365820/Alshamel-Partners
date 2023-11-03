<?php

namespace App\Http\Resources;

use App\Http\Resources\ScreenTreeProperty\ScreenTreePropertyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'description' => $this->description,
            'description_e' => $this->description_e,
            "model_id" => $this->model_id,
            "model_type" => $this->model_type,
            'socials' => $this->socials,
            'phones' => $this->phones,
            'job' => $this->job,
            'priority' => $this->priority,


        ];
    }
}
