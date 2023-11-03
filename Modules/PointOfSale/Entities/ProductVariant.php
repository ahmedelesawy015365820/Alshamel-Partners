<?php

namespace Modules\PointOfSale\Entities;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class ProductVariant extends Model implements HasMedia
{
    use HasFactory, LogTrait, MediaTrait, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'pos_product_variants';

    protected $appends = ['remaining_quantity'];

    protected $casts = ['purchase_price' => 'float' , 'selling_price' => 'float'];

    public function getRemainingQuantityAttribute()
    {
        $count_sales = $this->orderItem->where('variant_id', $this->id)->where('type', 'sales')->sum('quantity');
        $count_supplier = $this->orderItem->where('variant_id', $this->id)->where('type', 'receiving')->sum('quantity');
        return $remaining_quantity = $count_supplier + $count_sales;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function product_attribute_value()
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id');
    }

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class, 'variant_id', 'id');
    }
    public function orderItemQuantity()
    {
        return $this->hasOne(OrderItem::class, 'variant_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Product Variant')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
