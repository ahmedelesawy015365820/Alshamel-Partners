<?php

namespace Modules\PointOfSale\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class ProductAttributeValue extends Model
{
    use HasFactory, LogTrait,SoftDeletes;

    protected $guarded = ['id'];
    protected $table = "pos_product_attribute_values";

    public function order()
    {
        return $this->hasMany(Order::class, 'order_id');
    }
    


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Product Attribute Value')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
