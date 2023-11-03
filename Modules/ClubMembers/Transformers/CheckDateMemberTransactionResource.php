<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckDateMemberTransactionResource extends JsonResource
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
            // 'id' => $this->id,
            // 'member' => collect($this->member)->only(['id','first_name','second_name','third_name','last_name','family_name','membership_number','membership_date']),

            // 'serial_number' => $this->serial_number,

            // 'date' => $this->date,


            'id' => $this->id,
            'first_name' => $this->first_name,
            'second_name' => $this->second_name,
            'third_name' => $this->third_name,
            'last_name' => $this->last_name,
            'family_name' => $this->family_name,
            'full_name' => $this->full_name,
            'membership_date' => $this->membership_date,
            'membership_number' => $this->membership_number,
            'cmTransaction' => $this->cmTransaction ?? null,

        ];
    }
}
