<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PanelUsageRateReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $title  =
            $this->documentHeader->prefix. ' | ' .
            $this->documentHeader->customer->name . ' | ' .
            $this->quantity . ' | ' .
            $this->category->name . ' | ' .
            $this->governorate->name;

        return [
            'title'  => $title,
            'start'  => $this->date_from,
            'end'   => $this->date_to,
            'className' => '',
        ];
    }
}
