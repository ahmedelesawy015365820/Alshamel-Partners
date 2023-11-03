<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageReceiverContactResource extends JsonResource
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
            'message_request_id' => $this->message_request_id,
            'manager_employee_id' => $this->manager_employee_id,
            'messageRequest' => $this->messageRequest,
            'ManagerEmployee' => $this->ManagerEmployee,
        ];
    }
}
