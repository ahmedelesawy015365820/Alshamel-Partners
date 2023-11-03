<?php

namespace Modules\Stock\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory, LogTrait;
    protected $fillable = [
        'name',
        'name_e',
        'stock_balance'
    ];
}
