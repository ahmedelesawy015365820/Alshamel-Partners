<?php

namespace Modules\Custody\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'status' => $this->status,
            'default_amount' => $this->default_amount,
            'creation_user_id' => $this->creation_user_id,
            'last_update_user_id' => $this->last_update_user_id,
            'types' => TypeResource::collection($this->types),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
