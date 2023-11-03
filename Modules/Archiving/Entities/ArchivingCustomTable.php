<?php

namespace Modules\Archiving\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;

class ArchivingCustomTable extends Model
{
    use HasFactory,LogTrait;

    protected $table = 'Archiving_custom_tables';

    protected $guarded = ['id'];

    protected $casts = ["columns" => "json"];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Club Members Custom Table')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
