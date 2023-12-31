<?php

namespace App\Http\Resources\FinancialYear;

use Illuminate\Http\Resources\Json\JsonResource;

class FinancialYearResource extends JsonResource
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

            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active'=>$this->is_active,
            'year'=>$this->year,
            'due_date'=>$this->due_date,


        ];
    }
}
