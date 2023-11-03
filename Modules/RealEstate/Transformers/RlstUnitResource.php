<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RlstUnitResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'description' => $this->description,
            'description_e' => $this->description_e,
            'unit_ty' => $this->unit_ty,
            'unit_area' => $this->unit_area,
            'building_id' => $this->building_id,

            "building" => $this->building,
            'unit_status_id' => $this->unit_status_id,
            "unit_status" => $this->unitStatus,
            'rooms' => $this->rooms,
            "unit_net_area" => $this->unit_net_area,
            'path' => $this->path,
            'view' => $this->view,
            'floor' => $this->floor,
            'finishing' => $this->finishing,
            'properties' => $this->properties,
            'module' => $this->module,

            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
            "video_link" => $this->video_link,
        ];
    }
}
