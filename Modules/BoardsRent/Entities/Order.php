<?php

namespace Modules\BoardsRent\Entities;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Order extends Model implements HasMedia
{
    use MediaTrait, LogTrait, HasFactory;

    protected $fillable = [
        'branch_id',
        'serial_id',
        'status_id',
        'customer_id',
        'salesman_id',
        'sell_method_id',
        'document_id',
        'date',
        'note',
        'discount',
        'paid',
        'due',
        'total',
        'is_stripe',
        "serial_number",
        "is_quotation",
        "quotation_number",
        "company_id",
    ];

//    protected $casts = [
//        'is_stripe' => \App\Enums\BooleanStatus::class,
//        "is_quotation" => \App\Enums\BooleanStatus::class,
//    ];

    protected $table = 'boards_rent_orders';

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    public function serial()
    {
        return $this->belongsTo(\App\Models\Serial::class);
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\Status::class);
    }

    public function customer()
    {
        return $this->belongsTo(\Modules\BoardsRent\Entities\Customer::class);
    }

    public function salesman()
    {
        return $this->belongsTo(\App\Models\Salesman::class);
    }

    public function sellMethod()
    {
        return $this->belongsTo(\Modules\BoardsRent\Entities\SellMethod::class);
    }

    public function document()
    {
        return $this->belongsTo(\App\Models\Document::class);
    }

    public function details()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\OrderDetails::class);
    }

    public function hasChildren()
    {
        return $this->details->count();
    }
}
