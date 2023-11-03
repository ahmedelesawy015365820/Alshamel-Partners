<?php

namespace App\Http\Resources\DocumentModuleType;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentModuleTypeResource extends JsonResource
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

            "id"       => $this->id,
            "name"     => $this->name,
            "name_e"   => $this->name_e,
            "title"    => $this->title,
            "title_e"  => $this->title_e,
            "db_table" => $this->db_table,

        ];
    }
}
