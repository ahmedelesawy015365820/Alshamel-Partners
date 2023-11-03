<?php

namespace Modules\RealEstate\Entities;

use App\Models\Branch;
use App\Models\Document;
use App\Models\GeneralCustomer;
use App\Models\Salesman;
use App\Models\Serial;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Spatie\Activitylog\LogOptions;

class RlstInvoice extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $table = "rlst_invoices";

    protected $fillable = [
        "branch_id",
        "company_id",
        "document_id",
        "serial_id",
        "date",
        "customer_id",
        "salesman_id",
        "payment_plan_id",
        "building_id",
        "unit_id",
        "plan_installments_id",
        "start_date",
        "end_date",
        "flat_amount",
        "paid_amount",
        "module_type",
        "serial_number",
        "payment_method_id",
        "prefix",

    ];

    // relations
    public function salesman()
    {
        return $this->belongsTo(\App\Models\Salesman::class);
    }

    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class);
    }

    public function paymentPlan()
    {
        return @$this->belongsTo(\Modules\RecievablePayable\Entities\RpInstallmentPaymentPlan::class);
    }

    public function contracts()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstContract::class);
    }

    public function units()
    {
        return $this->belongsToMany(RlstReservationUnit::class, "unit_code");
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    public function serial()
    {
        return $this->belongsTo(\App\Models\Serial::class);
    }

    public function building()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstBuilding::class);
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstUnit::class);
    }

    public function document()
    {
        return $this->belongsTo(\App\Models\Document::class);
    }

    public function planInstallment()
    {
        return $this->belongsTo(\Modules\RecievablePayable\Entities\RpPaymentPlanInstallment::class);
    }

    public function items()
    {
        return $this->belongsToMany(RlstItem::class, 'rlst_invoices_item', 'invoice_id', 'item_id')
            ->withPivot('quantity', 'amount')
            ->using(RlstInvoiceItem::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(RlstInvoiceItem::class, 'invoice_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(\App\Models\PaymentMethod::class, 'payment_method_id');
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class, 'invoice_id');
    }

    public function breakDowns()
    {
        return $this->hasMany(RpBreakDown::class, 'break_id');
    }

    // public function hasChildren()
    // {
    //     return $this->contracts()->count() > 0 ||
    //     $this->units()->count() > 0 ||
    //     $this->items()->count() > 0 ||
    //     $this->invoiceItems()->count() > 0 ||
    //     $this->transactions()->count() > 0 ||
    //     $this->breakDowns()->count() > 0
    //     ;

    // }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->contracts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contracts',
                'count' => $this->contracts()->count(),
                'ids' => $this->contracts()->pluck('id')->toArray()
            ];
        }
        if ($this->units()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'units',
                'count' => $this->units()->count(),
                'ids' => $this->units()->pluck('id')->toArray()
            ];
        }
        if ($this->items()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'items',
                'count' => $this->items()->count(),
                'ids' => $this->items()->pluck('id')->toArray()
            ];
        }
        if ($this->invoiceItems()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'invoiceItems',
                'count' => $this->invoiceItems()->count(),
                'ids' => $this->invoiceItems()->pluck('id')->toArray()
            ];
        }
        if ($this->transactions()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'transactions',
                'count' => $this->transactions()->count(),
                'ids' => $this->transactions()->pluck('id')->toArray()
            ];
        }
        if ($this->breakDowns()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'breakDowns',
                'count' => $this->breakDowns()->count(),
                'ids' => $this->breakDowns()->pluck('id')->toArray()
            ];
        }

        return $relationsWithChildren;
    }

    // scopes

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Invoice')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
