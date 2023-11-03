<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_currencies';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id', 'name', 'name_e', 'code', 'code_e', 'fraction', 'fraction_e', 'fraction_no', 'symbol', 'symbol_e', 'is_default', 'is_active');
    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Currency')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
