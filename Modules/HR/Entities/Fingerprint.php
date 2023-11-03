<?php

namespace Modules\HR\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;
use Spatie\Activitylog\LogOptions;
use App\Traits\sendMessage;

class Fingerprint extends Model
{
    use HasFactory, LogTrait, sendMessage;

    protected $guarded = ['id'];

    protected $table = 'hr_fingerprint';

    


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('End Service')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
