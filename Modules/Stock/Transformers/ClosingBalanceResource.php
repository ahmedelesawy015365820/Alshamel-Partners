<?php

namespace Modules\Stock\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Stock\StoctRelationResource;
use App\Models\StockSalePurchaseDetail;
use App\Models\ClosingBalance;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class ClosingBalanceResource extends JsonResource
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
            'stock' => new  StoctRelationResource($this->stock),
            'stock_id' => $this->stock_id,
            'date' => $this->date,
            'amount' => $this->amount,
            'total_amount' => $this->total_amount,
            'stock_count' => $this->getStockCount($this->date, $this->stock_id),
        ];
    }
    public function getStockCount($date, $stock)
    {
        $stock = ClosingBalance::where('date', $date)->get()->count();
        return $stock;
    }


}
