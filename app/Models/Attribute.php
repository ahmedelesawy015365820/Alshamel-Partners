<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PointOfSale\Entities\ProductVariant;

class Attribute extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_attributes';

    protected $guarded = ['id'];


    public function scopeData($query)
    {
        return $query
            ->select('id','name','name_e');
    }

    public function children()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function product_variant()
    {
        return $this->hasMany(ProductVariant::class, 'attribute_id');
    }

    public function documentHeader()
    {
        return $this->belongsToMany(DocumentHeader::class, 'general_attendant_document_headers','document_header_id','attendant_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

//        if ($this->user()->count() > 0) {
//            $relationsWithChildren[] = [
//                'relation' => 'user',
//                'count' => $this->user()->count(),
//                'ids' => $this->user()->pluck('id')->toArray()
//            ];
//        }

//        if ($this->product_variant()->count() > 0) {
//            $relationsWithChildren[] = [
//                'relation' => 'product_variant',
//                'count' => $this->product_variant()->count(),
//                'ids' => $this->product_variant()->pluck('id')->toArray()
//            ];
//        }
        if ($this->documentHeader()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentHeader',
                'count' => $this->documentHeader()->count(),
                'ids' => $this->documentHeader()->pluck('id')->toArray()
            ];
        }


        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Variant Attribute')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
