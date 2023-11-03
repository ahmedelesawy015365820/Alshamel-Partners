<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class Store extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $table = 'general_stores';

    protected $fillable = [
        "name_e",
        "name",
        "company_id",
        "branch_id",
        'is_active',

    ];

    public function scopeData($query)
    {
        return $query->select(
            "name_e",
            "name",
            "branch_id",
            'is_active'
        )->with('branch:id,name,name_e');
    }

    protected $casts = [
        'is_active' => 'App\Enums\IsActive',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function serials()
    {
        return $this->hasMany(Serial::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Store')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    // public function hasChildren()
    // {
    //     $h = $this->serials()->count() > 0;
    //     return $h;
    // }
}
