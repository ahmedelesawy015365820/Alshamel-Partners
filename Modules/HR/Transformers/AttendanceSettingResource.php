<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceSettingResource extends JsonResource
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
            'pre_in' => $this->pre_in,
            'post_in' => $this->post_in,
            'absent_minutes' => $this->absent_minutes,
            'pre_out' => $this->pre_out,
            'post_out' => $this->post_out,
            'max_out' => $this->max_out,
        ];
    }
}
