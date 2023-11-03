<?php

namespace Modules\RecievablePayable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RecievablePayableCustomTableResource extends JsonResource
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
            'table_name' => $this->table_name,
            'company_id' => $this->company_id,
            'columns' => $this->columns,
        ];
    }
}
