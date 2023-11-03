<?php

namespace App\Http\Resources\BankAccount;

use App\Http\Resources\Bank\RelationBankResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RelationBankAccountResource extends JsonResource
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
            'bank' =>new RelationBankResource($this->bank),
            'account_number' => $this->account_number,
        ];
    }
}
