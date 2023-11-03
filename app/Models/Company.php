<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e', 'is_active', 'client_id','url', 'logo','address', 'phone', 'cr', 'tax_id',  'vat_no', 'email','website'
        );
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function serials()
    {
        return $this->hasMany(Serial::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function customTables()
    {
        return $this->hasMany(GeneralCustomTable::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function hasChildren()
    {
        return $this->stores()->count() > 0 ||
        $this->serials()->count() > 0 ||
        $this->branches()->count() > 0 ||
        $this->customTables()->count() > 0 ||
        $this->documents()->count() > 0;
    }

}
