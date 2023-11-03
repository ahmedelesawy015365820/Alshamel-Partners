<?php

namespace Modules\RecievablePayable\Entities;

use App\Models\DocumentModuleType;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RpOpeningBalance extends Model
{
    use HasFactory,LogTrait,SoftDeletes;

    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(\App\Models\GeneralCustomer::class,'customer_id','id');
    }
    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class,'currency_id','id');
    }
    public function breakDowns()
    {
        return $this->hasMany(RpBreakDown::class,'break_id');
    }


//    public function document()
//    {
//        return $this->belongsTo(Document::class,'break_id');
//    }
    // public function hasChildren()
    // {
    //     return $this->details()->count() > 0  ;

    // }

    // public function hisChildren()
    // {
    //     return $this->breakDowns()->count() > 0;
    // }

    // protected static function newFactory()
    // {
    //     return \Modules\RecievablePayable\Database\factories\RpOpeningBalanceFactory::new();
    // }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->breakDowns()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'breakDowns',
                'count' => $this->breakDowns()->count(),
                'ids' => $this->breakDowns()->pluck('id')->toArray()
            ];
        }

        return $relationsWithChildren;
    }
}
