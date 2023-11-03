<?php

namespace Modules\Booking\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class Floor extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'booking_floors';

    protected $guarded = ['id'];

    public function units()
    {
        return $this->hasMany(Unit::class, 'booking_floor_id');
    }
    public function hasChildren()
    {
        return$this->units()->count() > 0 ;
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Setting')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
