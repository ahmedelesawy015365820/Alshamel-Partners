<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralCustomTable extends Model
{
    use HasFactory,LogTrait;

    protected $table = 'general_custom_tables';

    protected $guarded = ['id'];

    protected $casts = ["columns" => "json"];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('General Custom Table')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }


}
