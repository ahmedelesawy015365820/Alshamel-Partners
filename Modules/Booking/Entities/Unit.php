<?php

namespace Modules\Booking\Entities;

use App\Models\DocumentHeaderDetail;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class Unit extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $table = 'booking_units';
    protected $guarded = ['id'];

    // relations

    public function unitStatus()
    {
        return $this->belongsTo(\App\Models\Status::class);
    }
    public function bookingUnitStatus()
    {
        return $this->belongsTo(\Modules\Booking\Entities\UnitStatus::class, 'unit_status_id', 'id');
    }

    public function documentHeaderDetails()
    {
        return $this->hasMany(DocumentHeaderDetail::class, 'unit_id');
    }

    public function Detail()
    {
        return $this->hasOne(DocumentHeaderDetail::class, 'unit_id')->latest();
    }

    public function DetailFilter()
    {
        return $this->hasOne(DocumentHeaderDetail::class, 'unit_id');
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'booking_floor_id');
    }

    public function hasChildren()
    {
        return$this->documentHeaderDetails()->count() > 0 ;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Booking Unit')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
