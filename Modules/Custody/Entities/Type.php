<?php

namespace Modules\Custody\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'cus_types';

    protected $fillable = [
        'name',
        'name_e',
        'company_id',
    ];

    //relations
    public function items()
    {
        return $this->belongsToMany(Item::class, 'cus_item_type', 'type_id', 'item_id');
    }

    // functions

    public function children()
    {
        return $this->items()->count() > 0;
    }

}
