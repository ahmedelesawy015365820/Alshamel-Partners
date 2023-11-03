<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $table = "general_banks";
    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id', 'name', 'name_e', 'swift_code', 'country_id')
            ->with('country:id,name,name_e');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    // public function hasChildren()
    // {
    //     return $this->bankAccounts()->count() > 0;
    // }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->bankAccounts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'bankAccounts',
                'count' => $this->bankAccounts()->count(),
                'ids' => $this->bankAccounts()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Bank')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
