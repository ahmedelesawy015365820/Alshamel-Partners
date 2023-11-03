<?php

namespace Modules\Stock\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogTrait;

class ClosingBalance extends Model
{
    use HasFactory,LogTrait;
    protected $fillable = [
        'stock_id',
        'date',
        'amount',
    ];
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }
}
