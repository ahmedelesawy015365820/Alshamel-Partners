<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'content' => $this->content,
            'content_e' => $this->content_e,
            'module'=> $this->module,
            'message_type_id'=> $this->message_type_id,
            'messageType'=> $this->messageType,

        ];
    }
}
