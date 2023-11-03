<?php

namespace Modules\GL\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class GlCoaResource extends JsonResource
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
            'id'=>$this->id,
            'acc_no' => $this->acc_no,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'parent_no' => $this->parent_no,
            'accounttype_id' => $this->accounttype_id,
        ];
    }
}
