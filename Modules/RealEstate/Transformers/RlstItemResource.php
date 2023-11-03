<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RlstItemResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'code_number' => $this->code_number,
            'type' => $this->type,
            'price' => $this->price,
            "unit_id" => $this->unit_id,
            "unit" => $this->units,
            "units" => $this->units,
            "categories" => $this->categories,
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
        ];
    }

}
