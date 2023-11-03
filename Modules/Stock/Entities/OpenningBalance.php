<?php

namespace Modules\Stock\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogTrait;

class OpenningBalance extends Model
{
    use HasFactory, LogTrait;
    protected $fillable = [
        'stock_id',
        'wallet_id',
        'date',
        'qty',
        'price',
        'net',
        'currency_id',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'id');
    }
}
