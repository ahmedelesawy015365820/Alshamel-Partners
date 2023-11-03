<?php

namespace App\Models;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PointOfSale\Entities\Order;
use Modules\RecievablePayable\Entities\RpOpeningBalance;
use Spatie\MediaLibrary\HasMedia;

class Supplier extends Model implements HasMedia
{
    use HasFactory, MediaTrait, LogTrait;
    protected $table = 'general_suppliers';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id','name','name','phone','email','country_id','city_id','rp_code','nationality','bank_account_id',
                'contact_person','contact_phone','national_id','whatsapp','passport_no','note','code_country','facebook',
                'instagram','linkedin','snapchat','twitter','website','salesman_id','employee_id','customer_source_id',
                'customer_group_id','is_supplier','sector_id','type','address','id_number','tax_record','passport_expiry_date',
                'visa_no'
            )
            ->with([
                'sector:id,name,name_e','customer_source:id,name,name_e','salesmen:id,name,name_e',
                'employee:id,name,name_e','country:id,name,name_e','city:id,name,name_e',
                'bankAccount:id,account_number','customerGroup:id,name,name_e',
                'customer_main_category:id,name,name_e','customer_sub_category:id,name,name_e',
            ]);
    }

    public function opening_balances()
    {
        return $this->hasMany(RpOpeningBalance::class, 'customer_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);

    }

    public function customer_main_category()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_main_category_id');
    }

    public function customer_sub_category()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_sub_category_id');
    }

    public function salesmen()
    {
        return $this->belongsTo(Salesman::class, 'salesman_id');
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class,'customer_group_id');
    }

    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class,'customer_id');

    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }


    public function hasChildren()
    {
       return $this->customer()->count() > 0;
    }
}
