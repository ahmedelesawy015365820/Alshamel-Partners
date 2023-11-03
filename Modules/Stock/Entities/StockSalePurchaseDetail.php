<?php

namespace Modules\Stock\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSalePurchaseDetail extends Model
{
    use HasFactory, LogTrait;

    protected $fillable = [
        'wallet_id',
        'company_id',
        'stock_id',
        'date',
        'time',
        'type',
        'note',
        'qty',
        'price',
        'fixed_commission',
        'other_commission',
        'net_value',
        'trade_average',
        'old_qty',
        "old_sell_qty",
        "old_sell_price",
        'old_price',
        'old_average',
        'new_qty',
        'new_average',
        'profit',
    ];

    /**
     * Get the wallet that owns the StockSalePurchaseDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'id');
    }


    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }
    public function closing()
    {
        return $this->belongsTo(ClosingBalance::class, 'stock_id', 'stock_id')->orderBy('id', 'desc');
    }
}
