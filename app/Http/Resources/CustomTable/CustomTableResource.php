<?php

namespace App\Http\Resources\CustomTable;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomTableResource extends JsonResource
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
            'id'          => $this->id,
            'table_name'  => $this->table_name,
            'company_id'  => $this->company_id,
            'columns'     => $this->columns,
        ];
    }
}
