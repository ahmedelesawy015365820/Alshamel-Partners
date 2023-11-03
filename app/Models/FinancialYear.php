<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class FinancialYear extends Model
{
    use HasFactory, SoftDeletes, LogTrait;
    protected $table = 'general_financial_years';

    protected $fillable = [
        'name',
        'name_e',
        'start_date',
        'end_date',
        "company_id",
        "year",
        'is_active',
        'due_date'
    ];

    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e', 'start_date', 'end_date', 'year', 'is_active');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Financial Year')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
