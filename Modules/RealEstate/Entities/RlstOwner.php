<?php

namespace Modules\RealEstate\Entities;

use App\Models\Country;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class RlstOwner extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
        /*  
        ->select(
                'id',
                'name',
                'name_e',
                'phone',
                'email',
                'rb_code',
                'phone_code',
                'contact_person',
                'contact_phones',
                'national_id',
                'whatsapp',
                'country_id',
                'city_id',
                'nationality_id',
                'bank_account_id',
                'categories',
                'attachments' )
            */    
            ->with(
                'country:id,name,name_e',
                'city:id,name,name_e',
                'nationality:id,name,name_e',
                'bankAccount:id,bank_id',
                'bankAccount.bank:id,name,name_e',
                //'wallets:id,name,name_e',
            );
           
    }

    // relations
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class);
    }

    public function nationality()
    {
        return $this->belongsTo(\App\Models\Country::class, 'nationality_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(\App\Models\BankAccount::class);
    }

    public function wallets()
    {
        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstWallet::class, 'rlst_wallet_owners', 'owner_id', 'wallet_id')
            ->withPivot('percentage')->wherePivotNull('deleted_at');
    }

    public function walletOwner()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstWalletOwner::class, "owner_id");
    }

    public function buildings()
    {
        $walletIds = $this->wallets()->pluck('rlst_wallet_owners.wallet_id');

        return \Modules\RealEstate\Entities\RlstBuilding::whereHas('wallets', function ($query) use ($walletIds) {
            $query->whereIn('wallet_id', $walletIds);
        })->get();
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->walletOwner()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'walletOwner',
                'count' => $this->walletOwner()->count(),
                'ids' => $this->walletOwner()->pluck('id')->toArray(),
            ];
        }
        return $relationsWithChildren;
    }

    // attributes

    protected function categories(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value),
            set: fn($value) => json_encode($value),
        );
    }

    protected function attachments(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value),
            set: fn($value) => json_encode($value),
        );
    }

    public function getPercentageAttribute()
    {
        $percentage = 0;
        foreach ($this->wallets as $wallet) {
            $percentage += $wallet->pivot->percentage;
        }
        return $percentage;
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Real Estate Owners')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
