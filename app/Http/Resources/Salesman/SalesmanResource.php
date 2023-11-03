<?php

namespace App\Http\Resources\Salesman;

use App\Http\Resources\Roles\RoleResource;
use App\Http\Resources\SalesmenType\RelationSalesmenTypeResource;
use App\Http\Resources\SalesmenType\SalesmenTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesmanResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "name_e" => $this->name_e,
            "salesmanType" => $this->salesmanType
        ];
    }
}
