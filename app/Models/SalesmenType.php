<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class SalesmenType extends Model
{
    use HasFactory, SoftDeletes, LogTrait;
    protected $table = 'general_salesmen_types';

    protected $fillable = [
        'name',
        'name_e',
        'is_employee',
        "company_id"
    ];

    protected $casts = [
        'is_employee' => '\App\Enums\IsDefault',
    ];

    public function salesmen()
    {
        return $this->hasMany(Salesman::class, 'salesman_type_id');
    }

    public function hasChildren()
    {
        $h = $this->salesmen()->count() > 0;
        return $h;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Salesmen Type')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
