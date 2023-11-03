<?php

namespace App\Http\Resources\BankAccount;

use App\Http\Resources\Employee\RelationEmployeeResource;
use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
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
            'bank_id' => $this->bank_id,
            'account_number' => $this->account_number,
            'phone' => $this->phone,
            'address' => $this->address,
            'email' => $this->email,
            'emp_id' => $this->emp_id,
            'rp_code' => $this->rp_code,
            'employee' =>$this->employee,
            'bank' => $this->bank,
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
        ];
    }
}
