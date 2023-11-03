<?php

namespace Modules\RecievablePayable\Entities;

use App\Models\BreakSettlement;
use App\Models\DocumentHeader;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Entities\RlstContract;
use Modules\RealEstate\Entities\RlstInvoice;
use Modules\RealEstate\Entities\RlstReservation;

class RpBreakDown extends Model
{
    use HasFactory,LogTrait,SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = ["terms" => "json"];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id','customer_id','break_id','break_type','debit','credit','instalment_date','total','document_id'
            )
            ->with([
                'documentHeader:id,prefix','document:id,name,name_e'
            ]);
    }



    public function document()
    {
        return $this->belongsTo(\App\Models\Document::class,'document_id',);
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class,'currency_id',);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\GeneralCustomer::class,'customer_id',);
    }

    public function installment_status()
    {
        return $this->belongsTo(RpInstallmentStatus::class,'installment_statu_id',);
    }

    public function installment_payment_type()
    {
        return $this->belongsTo(RpInstallmentPaymentType::class,'instalment_type_id',);
    }

    public function openingBalance()
    {
        return $this->belongsTo(RpOpeningBalance::class,'break_id',);
    }

    public function contract()
    {
        return $this->belongsTo(RlstContract::class,'break_id',);
    }

    public function invoice()
    {
        return $this->belongsTo(RlstInvoice::class,'break_id',);
    }
    public function paymentInvoice()
    {
        return $this->belongsTo(RlstInvoice::class,'invoice_id',);
    }
    public function reservation()
    {
        return $this->belongsTo(RlstReservation::class,'break_id',);
    }
    public function documentHeader()
    {
        return $this->belongsTo(DocumentHeader::class,'break_id',);
    }

    public function children()
    {
        return $this->hasMany(RpBreakDown::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(RpBreakDown::class, 'parent_id');
    }

    public function breakSettlements()
    {
        return  $this->hasMany(BreakSettlement::class, 'break_id');
    }

    public function hisChildren()
    {
        return $this->children()->count() > 0 || $this->breakSettlements() > 0;
    }

    public function scopeFilterBreak($query , $request)
    {
        return $query->where(function ($q) use ($request){
            $q->where(function ($q) use ($request){
                $q->when($request->customer_id,function ($q) use ($request){
                    $q->where('customer_id',$request->customer_id);
                });
            })->where(function ($q) use ($request){
                $q->when($request->document_id,function ($q) use ($request){
                    $q->where('document_id',$request->document_id);
                });
            })->where(function ($q) use ($request){
                $q->when($request->instalment_type_id,function ($q) use ($request){
                    $q->where('instalment_type_id',$request->instalment_type_id);
                });
            })->where(function ($q) use ($request){
                $q->when($request->break_type  ,function ($q) use ($request){
                    $q->where('break_type',$request->break_type);
                });
            })->where(function ($q) use ($request){
                $q->when($request->break_id  ,function ($q) use ($request){
                    $q->where('break_id',$request->break_id);
                });
            })->where(function ($q) use ($request){
                $q->when($request->invoice_id,function ($q) use ($request){
                    $q->where('invoice_id',$request->invoice_id);
                });
            })->where(function ($q) use ($request){
                $q->when($request->amount_status,function ($q) use ($request){
                    $q->where('amount_status',$request->amount_status);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->start_date && $request->end_date, function ($q) use ($request) {
                    $q->whereDate('instalment_date', ">=", $request->start_date)
                        ->whereDate('instalment_date', "<=", $request->end_date);

                });
            });
        });


    }

    // protected static function newFactory()
    // {
    //     return \Modules\RecievablePayable\Database\factories\RpBreakDownFactory::new();
    // }
}
