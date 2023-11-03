<?php

namespace Modules\Custody\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'cus_items';

    protected $fillable = [
        'name',
        'name_e',
        'model_type',
        'model_id',
        'status',
        'default_amount',
        'creation_user_id',
        'last_update_user_id',
        'company_id',

    ];

    protected $casts = [
        'default_amount' => 'double'

    ];

    //relations
    public function types()
    {
        return $this->belongsToMany(Type::class, 'cus_item_type', 'item_id', 'type_id');
    }

    public function employee()
    {
        return $this->morphedByMany(Employee::class, 'cus_items', 'cus_items', 'model_id', 'model_type');
    }

    public function user()
    {
        return $this->morphedByMany(User::class, 'cus_items', 'cus_items', 'model_id', 'model_type');
    }

    // functions
    public function children()
    {
        return false;
    }

}
