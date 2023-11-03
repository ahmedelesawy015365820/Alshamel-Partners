<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
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
            $this->key => [
                'id' => $this->id,
                'default_en' => $this->default_en,
                'default_ar' => $this->default_ar,
                'new_en' => $this->new_en,
                'new_ar' => $this->new_ar,
            ]
        ];
    }
}
