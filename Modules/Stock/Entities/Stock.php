<?php

namespace Modules\Stock\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogTrait;

class Stock extends Model
{
    use HasFactory, LogTrait;

    protected $fillable = [
        'name',
        'name_e',
        'stock_market_id',
        'sector_id',
        'company_id',
        'symbol',
    ];

    public function stockMarket()
    {
        return $this->belongsTo(StockMarket::class, 'stock_market_id', 'id');
    }
    public function stockSector()
    {
        return $this->belongsTo(StockSector::class, 'sector_id', 'id');
    }

    public function closing()
    {
        return $this->belongsTo(ClosingBalance::class, 'stock_id', 'id');
    }

    public function sspd(){
        return $this->hasMany (StockSalePurchaseDetail::class,'stock_id');
    }


}
