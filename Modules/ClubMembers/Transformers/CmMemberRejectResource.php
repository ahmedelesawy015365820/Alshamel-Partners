<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CmMemberRejectResource extends JsonResource
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
            'date' => $this->date,
            'note' => $this->note,
            'session_number' => $this->session_number,
            'entity' => $this->entity,
            'prefix' => $this->prefix,
            'member' => collect($this->member)->only(['id','first_name','second_name','third_name','last_name','family_name','full_name','membership_number']),
            'branch' => collect($this->branch)->only(['id','name','name_e']),
            'serial' => collect($this->serial)->only(['id','name','name_e']),
            'discharge_reson' => collect($this->dischargeReson)->only(['id','name','name_e']),


        ];
    }
}
