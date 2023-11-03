<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class RlstWalletOwner extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $fillable = [
        'wallet_id',
        "owner_id",
        "percentage",
    ];

    // relations

    public function wallet()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstWallet::class, 'wallet_id');
    }

    public function owner()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstOwner::class, 'owner_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Wallet Owner')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
