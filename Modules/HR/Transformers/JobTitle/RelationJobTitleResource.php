<?php

namespace Modules\HR\Transformers\JobTitle;

use Illuminate\Http\Resources\Json\JsonResource;

class RelationJobTitleResource extends JsonResource
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
            'name_e' => $this->name_e
        ];
    }
}
