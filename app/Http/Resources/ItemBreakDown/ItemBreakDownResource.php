<?php

namespace App\Http\Resources\ItemBreakDown;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\Panel\RelationBRentPanelResource;
use Modules\BoardsRent\Transformers\PanelResource;

class ItemBreakDownResource extends JsonResource
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
            "id" => $this->id,
            "date_from"=> $this->date_from,
            "date_to"=> $this->date_to,
            "break_id"=> $this->break_id,
            "module_type"=> $this->module_type,
            "serial_number"=> $this->serial_number,
            "panel"=> new RelationBRentPanelResource($this->panel)

        ];
    }
}
