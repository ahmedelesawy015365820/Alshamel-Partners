<?php

namespace Modules\BoardsRent\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'company_id',
        'category_id',
        'governorate_id',
        'package_id',
        'quantity',
        'from',
        'to',
        'price',
        'status',
    ];

    protected $table = 'boards_rent_order_details';

    protected $casts = [
        'status' => \App\Enums\BooleanStatus::class,
    ];

    public function order()
    {
        return $this->belongsTo(\Modules\BoardsRent\Entities\Order::class);
    }

    public function governorate()
    {
        return $this->belongsTo(\App\Models\Governorate::class);
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function package()
    {
        return $this->belongsTo(\Modules\BoardsRent\Entities\Package::class);
    }

    public function panels()
    {
        return $this->belongsToMany(\Modules\BoardsRent\Entities\Panel::class, 'boards_rent_order_detail_panel', 'order_detail_id', 'panel_id');
    }

}
