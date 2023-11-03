<?php

namespace App\Http\Resources\Store;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Branch\RelationBranchResource;
use App\Http\Resources\Company\CompanyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'is_active' => $this->is_active,
             'company_id' => $this->company_id,
            'branch' => $this->branch,
            "branch_id"=>$this->branch_id
        ];
    }
}
