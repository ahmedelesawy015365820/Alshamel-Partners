<?php

namespace Modules\PointOfSale\Entities;

use App\Models\Attribute;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Group;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\User;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, LogTrait, MediaTrait, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'pos_products';

    protected $appends = ['item_sold', 'sub_total', 'tax', 'discount', 'total', 'item_purchased', 'total_purchased'];

    //relations

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function product_variant()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function product_attribute_value()
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'pos_product_attribute_values', 'product_id', 'attribute_id')->withPivot('values')
            ->withTimestamps();
    }
    //    public function dataAttributes()
    //    {
    //        $branch = $this->branch()->get();
    //        return $branch;
    //
    //    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');

    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        $salesOrderItemsCount = $this->orderItems()->where('type', 'sales')->count();
        if ($salesOrderItemsCount > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orderItems',
                'count' => $salesOrderItemsCount,
                'ids' => $this->orderItems()->where('type', 'sales')->pluck('id')->toArray(),
            ];
        }

        $otherOrderItemsCount = $this->orderItems()->count() - $salesOrderItemsCount;
        if ($otherOrderItemsCount > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orderItems',
                'count' => $otherOrderItemsCount,
                'ids' => $this->orderItems()->where('type', '<>', 'sales')->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function dataAttributes()
    {
        $attributes = [];
        $branch_id = Branch::whereHas('products')->first()->id;
        $attributes['branch_id'] = $branch_id;
        return $attributes;
    }

    public function scopeAttribute($query, $request)
    {
        $attributes = $this->dataAttributes();

        return $query->where(function ($q) use ($request, $attributes) {
            if ($request->branch_id) {
                $q->where('branch_id', $request->branch_id);
            }
            if (!$request->branch_id) {
                $q->where('branch_id', $attributes['branch_id']);
            }
            if ($request->categories_id) {
                $q->whereIn('category_id', $request->categories_id);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Product')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function getItemSoldAttribute($key)
    {
        $product_id = Product::where('id', $this->id)->pluck('id')->toArray();

        return OrderItem::where('type', 'sales')->whereIn('product_id', $product_id)
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'sales')
                    ->where('status', 'done');
            })->sum('quantity');
    }

    public function getSubTotalAttribute($key)
    {
        $product_ids = Product::where('id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('sub_total');
    }

    public function getTaxAttribute($key)
    {
        $product_ids = Product::where('id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('total_tax');
    }

    public function getDiscountAttribute($key)
    {
        $product_ids = Product::where('id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('all_discount');
    }
    public function getTotalAttribute($key)
    {
        $product_ids = Product::where('id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'sales')
            ->where('status', 'done')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'sales')->whereIn('product_id', $product_ids);
            })->sum('total');
    }

    public function getItemPurchasedAttribute($key)
    {
        $product_id = Product::where('id', $this->id)->pluck('id')->toArray();

        return OrderItem::where('type', 'receiving')->whereIn('product_id', $product_id)
            ->whereHas('order', function ($query) {
                $query->where('order_type', 'receiving')
                    ->where('status', 'done')
                    ->where('type', 'supplier');
            })->sum('quantity');
    }
    public function getTotalPurchasedAttribute($key)
    {
        $product_ids = Product::where('id', $this->id)->pluck('id')->toArray();

        return Order::where('order_type', 'receiving')
            ->where('status', 'done')
            ->where('type', 'supplier')
            ->whereHas('items', function ($query) use ($product_ids) {
                $query->where('type', 'receiving')->whereIn('product_id', $product_ids);
            })->sum('total');
    }
}
