<?php

namespace Modules\Stock\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;
class StockCustomTable extends Model
{
    use HasFactory,LogTrait;

    protected $table = 'St_custom_tables';

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
            ->useLogName('Real Estate Custom Table')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
