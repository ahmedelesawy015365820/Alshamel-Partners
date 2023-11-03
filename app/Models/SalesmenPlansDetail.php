<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class SalesmenPlansDetail extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_salesmen_plans_details';

    protected $fillable = [
        'plan_id',
        'amount_from',
        'amount_to',
        'commission_percent',
        'company_id',
        'is_default'
    ];

    public function scopeData($query)
    {
        return $query->select(
            'plan_id',
            'amount_from',
            'amount_to',
            'commission_percent',
            'is_default'
        )->with(['plan:id,name,name_e']);
    }


    public function plan()
    {
        return $this->belongsTo(SalesmenPlan::class, 'plan_id');
    }

    public function hasChildren()
    {
        return false;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Salesmen Plans Detail')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
