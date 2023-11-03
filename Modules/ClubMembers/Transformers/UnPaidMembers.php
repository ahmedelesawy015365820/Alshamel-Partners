<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UnPaidMembers extends JsonResource
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
            'first_name' => $this->first_name,
            'second_name' => $this->second_name,
            'third_name' => $this->third_name,
            'last_name' => $this->last_name,
            'family_name' => $this->family_name,
            'full_name' => $this->full_name,
            'membership_date' => $this->membership_date,
            'membership_number' => $this->membership_number,
            'transaction_id' => $this->lastCmTransaction ? $this->lastCmTransaction->id : '',
            'transaction_date' => $this->lastCmTransaction ? $this->lastCmTransaction->date : '',
            'transaction_amount' => $this->lastCmTransaction ? $this->lastCmTransaction->amount : '',
            'transaction_year_to' => $this->lastCmTransaction ? $this->lastCmTransaction->year_to : '',
            'transaction_serial_number' => $this->lastCmTransaction ? $this->lastCmTransaction->serial_number : ''
        ];

    }
}
