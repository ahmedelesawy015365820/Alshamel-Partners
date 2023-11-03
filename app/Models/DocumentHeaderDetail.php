<?php

namespace App\Models;

use App\Traits\LogTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\BoardsRent\Entities\Package;
use Modules\Booking\Entities\Unit;
use Modules\Booking\Entities\UnitStatus;

class DocumentHeaderDetail extends Model
{
    use HasFactory, SoftDeletes , LogTrait;

    protected $table = 'general_document_header_details';

    protected $guarded = ['id'];

//    protected $casts = [
//        "date_from" => 'date',
//        "date_to" => 'date'
//    ];

    public function setDateAttribute( $value ) {
        $this->attributes['date_from'] = (new Carbon($value))->format('Y-m-d');
    }

    public function unitStatus()
    {
        return $this->belongsTo(\App\Models\Status::class);
    }

    public function bookingUnitStatus()
    {
        return $this->belongsTo(\Modules\Booking\Entities\UnitStatus::class, 'unit_status_id', 'id');
    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Document Header Detail')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function documentHeader()
    {
        return $this->belongsTo(DocumentHeader::class,'document_header_id');
    }
    public function governorate()
    {
        return $this->belongsTo(Governorate::class,'governorate_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class,'package_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function itemBreakDowns()
    {
        return $this->hasMany(ItemBreakDown::class,'document_header_detail_id');
    }
    public function item()
    {
        return $this->belongsTo(GeneralItem::class,'item_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }

    public function scopeReportDetail($query, $request)
    {
        return $query->where(function ($q) use ($request){
            if($request->document_id){
                $q->whereHas('documentHeader',function ($q) use ($request){
                    $q->where('document_id' ,$request->document_id);
                });
            }
            if($request->document_detail_type){
                $q->whereRelation('documentHeader',function ($q) use ($request){
                    $q->whereRelation('document' ,function ($q) use ($request){
                        $q->where('document_detail_type',$request->document_detail_type)
                            ->where(function ($q) use ($request){
                                $q->where('attributes->commission','!=',"0")
                                    ->orWhere('attributes->unrealized_revenue','!=',"0")
                                    ->orWhere('attributes->revenue','!=',"0")
                                    ->orWhere('attributes->unrealized_commission','!=',"0");
                            });
                    });
                });
            }
            if ($request->governorate_id){
                $q->where('governorate_id',$request->governorate_id);
            }
            if ($request->category_id){
                $q->where('category_id',$request->category_id);
            }
            if ($request->is_stripe){
                $q->where('is_stripe',$request->is_stripe);
            }
            if ($request->package_id){
                $q->where('package_id',$request->package_id);
            }
            if($request->sell_method_id){
                $q->whereHas('documentHeader',function ($q) use ($request){
                    $q->where('sell_method_id' ,$request->sell_method_id );
                });
            }
            if($request->customer_id){
                $q->whereHas('documentHeader',function ($q) use ($request){
                    $q->where('customer_id' ,$request->customer_id);
                });
            }
            if($request->employee_id){
                $q->whereHas('documentHeader',function ($q) use ($request){
                    $q->where('employee_id' ,$request->employee_id);
                });
            }
        });
    }

    public function scopeReport($query, $request)
    {
        return $query
            ->where(function ($q) use ($request){
                $q->when($request->governorate_id,function ($q) use ($request){
                    $q->where('governorate_id',$request->governorate_id);
                });
            })->where(function ($q) use ($request){
                $q->when($request->category_id,function ($q) use ($request){
                    $q->where('category_id',$request->category_id);
                });
            })->where(function ($q) use ($request){
                $q->when($request->is_stripe,function ($q) use ($request){
                    $q->where('is_stripe',$request->is_stripe);
                });
            })->where(function ($q) use ($request){
                $q->when($request->package_id,function ($q) use ($request){
                    $q->where('package_id',$request->package_id);
                });
            })->where(function ($q) use ($request){
                $q->when($request->sell_method_id,function ($q) use ($request){
                    $q->whereHas('documentHeader',function ($q) use ($request){
                        $q->where('sell_method_id' ,$request->sell_method_id );
                    });
                });
            })->where(function ($q) use ($request){
                $q->when($request->customer_id,function ($q) use ($request){
                    $q->whereHas('documentHeader',function ($q) use ($request){
                        $q->where('customer_id' ,$request->customer_id );
                    });
                });
            })->where(function ($q) use ($request){
                $q->when($request->employee_id,function ($q) use ($request){
                    $q->whereHas('documentHeader',function ($q) use ($request){
                        $q->where('employee_id' ,$request->employee_id );
                    });
                });
            })->where(function ($q) use ($request){
                $q->when($request->document_id,function ($q) use ($request){
                    $q->whereHas('documentHeader',function ($q) use ($request){
                        $q->where('document_id' ,$request->document_id );
                    });
                });
            })->where(function ($q) use ($request){
                $q->when($request->document_detail_type,function ($q) use ($request){
                    $q->whereRelation('documentHeader' ,function  ($q)  use ($request){
                        $q->whereRelation('document'   ,function   ($q)   use ($request){
                            $q->where('document_detail_type',$request->document_detail_type)
                                ->where(function ($q) use ($request){
                                    $q->where('attributes->commission','!=',"0")
                                        ->orWhere('attributes->unrealized_revenue','!=',"0")
                                        ->orWhere('attributes->revenue','!=',"0")
                                        ->orWhere('attributes->unrealized_commission','!=',"0");
                                });
                        });
                    });
                });

            });
    }

}
