<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ClubMembers\Transformers\StatusResource;

class CmMemberResource extends JsonResource
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
            'birth_date' => $this->birth_date,
            'national_id' => $this->national_id,
            'nationality_class' => $this->nationality_class,
            'phone_code' => $this->phone_code,
            'home_phone' => $this->home_phone,
            'work_phone' => $this->work_phone,
            'home_address' => $this->home_address,
            'work_address' => $this->work_address,
            'membership_date' => $this->membership_date,
            'membership_number' => $this->membership_number,
            'job' => $this->job,
            'degree' => $this->degree,
            'acceptance' => $this->acceptance,
            'session_date' => $this->session_date,
            'session_number' => $this->session_number,
            'applying_date' => $this->applying_date,
            'applying_number' => $this->applying_number,
            'sponsor_id' => $this->sponsor_id,
            'member_kind_id' => $this->member_kind_id,
            'financial_status_id' => $this->financial_status_id,
            "member_status_id" => $this->member_status_id,
            'member_type' => $this->memberType,
            'notes' => $this->notes,
            'gender' => $this->gender,
            'sponsors' => $this->sponsors,
            'membersType' => $this->memberType,
            'financial_status' => $this->financialStatus,
            "status" => new StatusResource($this->status),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cm_transaction_count' => $this->cm_transaction_count??null,
            'transaction' =>$this->lastCmTransaction

        ];
    }
}
