<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\RecievablePayable\Entities\RpPaymentPlanInstallment;

class Document extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_documents';

    protected $guarded = [''];

    protected $appends = ['relation_voucher_header'];

//    public function getRequired()
//    {
//        return $this->required == 1 ? 'Yes' : 'No';
//    }
//
//    public function getNeedApprove()
//    {
//        return $this->required == 1 ? 'Yes' : 'No';
//    }
//
//    public function getContusion()
//    {
//        return $this->contusion == 1 ? 'True' : 'False';
//    }
//    public function getIsDefault()
//    {
//        return $this->is_default == 1 ? 'Default' : 'Non Default';
//    }

    // public function getRequired()
    // {
    //     return $this->required == 1 ? 'Yes' : 'No';
    // }

    // public function getNeedApprove()
    // {
    //     return $this->required == 1 ? 'Yes' : 'No';
    // }

    // public function getContusion()
    // {
    //     return $this->contusion == 1 ? 'True' : 'False';
    // }
    // public function getIsDefault()
    // {
    //     return $this->is_default == 1 ? 'Default' : 'Non Default';
    // }


    protected $casts = ["attributes" => "json"];



    public function payment_plan_installments()
    {
        return $this->hasMany(RpPaymentPlanInstallment::class, 'doc_type_id');
    }

    public function orders()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\Order::class, 'doc_type_id');
    }

    public function documentHeader()
    {
        return $this->hasMany(DocumentHeader::class, 'document_id');
    }
    public function documentRelatedHeader()
    {
        return $this->hasMany(DocumentHeader::class, 'related_document_id');
    }

    public function documentRelateds()
    {
        return $this->belongsToMany(Document::class, 'general_document_related', 'document_id', 'document_related_id','id','id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'general_documents_approve_personal', 'document_id', 'employee_id','id','id');
    }

    public function voucherHeader()
    {
        return $this->hasMany(VoucherHeader::class, 'document_id');

    }

    public function documentModuleType()
    {
        return $this->belongsTo(DocumentModuleType::class, 'document_module_type_id');

    }

    public function getRelationVoucherHeaderAttribute($key)
    {
        return $this->voucherHeader()->count() > 0 ? 1 : 0;
    }

    public function documentCompanyModuleStatuses()
    {
        return $this->hasMany(DocumentCompanyModuleStatus::class,'document_id');

    }

    public function hasChildren()
    {
        return $this->orders->count() > 0 ||
        $this->payment_plan_installments->count() > 0 ||  $this->documentCompanyModuleStatuses->count() > 0;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Avenue')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

//    public function scopeCompany($query, $request)
//    {
//        return $query->where(function ($q) use ($request){
//           $q->when($request->company_id,function ($q) use ($request){
//               $q->where('company_id',$request->company_id);
//           }) ;
//        });
//    }

}
