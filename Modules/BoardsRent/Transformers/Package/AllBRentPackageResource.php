<?php

namespace Modules\BoardsRent\Transformers\Package;

use Illuminate\Http\Resources\Json\JsonResource;

class AllBRentPackageResource extends JsonResource
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
            'id'     => $this->id,
            'name'   => $this->name,
            'name_e' => $this->name_e,
            'code'           => $this->code,
            'price'          => $this->price,
            'note'           => $this->note,
            'category_id'    => $this->category_id,
            'governorate_id' => $this->governorate_id,
            'category' => collect($this->whenLoaded('category'))->only(['id','name','name_e']) ,
            'governorate' => $this->whenLoaded('governorate'),


        ];
    }
}
