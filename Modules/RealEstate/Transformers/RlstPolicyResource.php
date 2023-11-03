<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RlstPolicyResource extends JsonResource
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
            'description' => $this->description,
        ];
    }
}
