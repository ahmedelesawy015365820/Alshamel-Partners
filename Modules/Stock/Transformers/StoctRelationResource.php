<?php

namespace Modules\Stock\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\StockMarket\StockMarketRelationResource;
use App\Http\Resources\StockSector\StockSectorRelationResource;
class StoctRelationResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'stock_market_id' => new StockMarketRelationResource($this->stockMarket),
            // 'stock_market_id' => $this->stock_market_id ,
            'sector_id' => new StockSectorRelationResource($this->stockSector),
            // 'sector_id' => $this->sector_id,
            'symbol' => $this->symbol,
        ];
    }
}
