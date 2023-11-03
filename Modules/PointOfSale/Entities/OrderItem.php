<?php

namespace Modules\PointOfSale\Entities;

use App\Models\Tax;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class OrderItem extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = "pos_order_items";
    protected $appends = ['item_discount', 'item_count'];

    protected $casts = [
        'order_id'   => 'integer' ,
        'price'      => 'float',
        'product_id' => 'integer',
        'quantity'   => 'integer',
        'sub_total'  => 'float',
        'variant_id' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function child()
    {
        return $this->hasMany(OrderItem::class, 'parent_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'id');
    }

    public function getItemDiscountAttribute($key)
    {
        if ($this->discount == "0") {
            return $this->type == "sales" ? $this->price : 0;
        }
        return $this->type == "discount" ? "0" : $this->price - ($this->price * $this->discount / 100);

    }
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    public function getItemCountAttribute($key)
    {
        if ($this->type == "discount") {
            return 0;
        }
        return abs($this->quantity + $this->child->sum('quantity'));
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Order item')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
