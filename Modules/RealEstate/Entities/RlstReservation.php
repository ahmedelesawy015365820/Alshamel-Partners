<?php

namespace Modules\RealEstate\Entities;

use App\Models\Branch;
use App\Models\Document;
use App\Models\GeneralCustomer;
use App\Models\Salesman;
use App\Models\Serial;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class RlstReservation extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait, MediaTrait;

    protected $fillable = [
        "company_id",
        "branch_id",
        "document_id",
        "serial_id",
        "date",
        "customer_id",
        "salesman_id",
        "start_date",
        "end_date",
        "module_type",
        "serial_number",
        "prefix",
    ];

    // relations
    public function salesman()
    {
        return $this->belongsTo(Salesman::class);
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

    public function document()
    {
        return $this->belongsTo(\App\Models\Document::class);
    }

    public function breakDowns()
    {
        return $this->hasMany(RpBreakDown::class, 'break_id');
    }


    public function details()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstReservationDetail::class,
        'reservation_id', 'id');
    }

    // public function hasChildren()
    // {
    //     return $this->contracts()->count() > 0 ||
    //     $this->units()->count() > 0 ||
    //     $this->details()->count() > 0 ||
    //     $this->breakDowns()->count() > 0 ;

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

        if ($this->details()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'details',
                'count' => $this->details()->count(),
                'ids' => $this->details()->pluck('id')->toArray()
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

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Reservation')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
