<?php

namespace App\Http\Resources\DocumentHeader;

use Illuminate\Http\Resources\Json\JsonResource;

class NewDocumentHeaderResource extends JsonResource
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
            'branch_id' => $this->branch_id,
            'date' => $this->date,
            'prefix' => $this->prefix,
            'employee_id' => $this->employee_id,
            'customer_id' => $this->customer_id,
            'company_id' => $this->company_id,
            'branch' => $this->whenLoaded('branch'),
            'employee' => $this->whenLoaded('employee'),
            'customer' => collect($this->whenLoaded('customer'))->only(['id', 'name', 'name_e']),
            // 'details' => $this->documentHeaderDetails,
        ];
    }
}
