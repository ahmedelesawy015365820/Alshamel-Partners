<?php

namespace Modules\RealEstate\Entities;

use App\Models\Country;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RealEstate\Entities\RlstReservation;
use Spatie\Activitylog\LogOptions;

class RlstCustomer extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $fillable = [
        'name',
        'name_e',
        'phone',
        'email',
        'country_id',
        'company_id',
        'city_id',
        'rb_code',
        'nationality_id',
        'bank_account_id',
        'contact_person',
        'contact_phones',
        'national_id',
        'passport_no',
        'note1',
        'note2',
        'note3',
        'note4',
        'whatsapp',
        'categories',
        "attachments",

    ];

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

    public function reservations()
    {
        return $this->hasMany(RlstReservation::class, 'customer_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(\App\Models\BankAccount::class, 'bank_account_id');
    }
    // attributes

    protected function categories(): Attribute
    {
        return Attribute::make(
            get:fn($value) => json_decode($value),
            set:fn($value) => json_encode($value),
        );
    }

    protected function attachments(): Attribute
    {
        return Attribute::make(
            get:fn($value) => json_decode($value),
            set:fn($value) => json_encode($value),
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Real Estate Customers')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
