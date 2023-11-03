<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\OrderItem;
use Modules\PointOfSale\Entities\Product;

class Group extends Model
{

    use HasFactory, LogTrait;

    protected $table = 'general_groups';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e');
    }

    protected $appends = ['item_sold', 'sub_total', 'tax', 'discount', 'total', 'item_purchased', 'total_purchased'];

    public function products()
    {
        return $this->hasMany(Product::class, 'group_id');
    }
    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->products()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'products',
                'count' => $this->products()->count(),
                'ids' => $this->products()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Group')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function getItemSoldAttribute($key)
    {
        $product_id = $this->products->where('group_id', $this->id)->pluck('id')->toArray();

        return OrderItem::where('type', 'sales')->whereIn('product_id', $product_id)
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'sales')
                    ->where('status', 'done');
            })->sum('quantity');
    }

    public function getSubTotalAttribute($key)
    {
        $product_ids = $this->products->where('group_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('sub_total');
    }

    public function getTaxAttribute($key)
    {
        $product_ids = $this->products->where('group_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('total_tax');
    }

    public function getDiscountAttribute($key)
    {
        $product_ids = $this->products->where('group_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('all_discount');
    }
    public function getTotalAttribute($key)
    {
        $product_ids = $this->products->where('group_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('total');
    }

    public function getItemPurchasedAttribute($key)
    {
        $product_id = $this->products->where('group_id', $this->id)->pluck('id')->toArray();

        return OrderItem::where('type', 'receiving')->whereIn('product_id', $product_id)
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'receiving')
                    ->where('status', 'done')
                    ->where('type', 'supplier');
            })->sum('quantity');
    }
    public function getTotalPurchasedAttribute($key)
    {
        $product_ids = $this->products->where('group_id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'receiving')
            ->where('status', 'done')
            ->where('type', 'supplier')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'receiving')->whereIn('product_id', $product_ids);
            })->sum('total');
    }
}
