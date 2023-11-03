<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class RlstReservationDetail extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait, MediaTrait;

    protected $fillable = [
        "building_id",
        "unit_id",
        "installment_payment_plans_id",
        "reservation_value",
        "unit_value",
        "reservation_id",
    ];

    protected $table = "rlst_reservation_details";

    public function building()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstBuilding::class,
            'building_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstUnit::class,
            'unit_id', 'id');
    }

    public function planInstallment()
    {
        return $this->belongsTo(\Modules\RecievablePayable\Entities\RpInstallmentPaymentPlan::class);
    }

    public function reservation()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstReservation::class, 'reservation_id', 'id');
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
