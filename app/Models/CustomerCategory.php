<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCategory extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];
    protected $table = 'general_customer_categories';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeData($query)
    {
        return $query
            ->select('id', 'name', 'name_e', 'parent_id')
            ->with('parent:id,name,name_e');
    }

    public function parent()
    {
        return $this->belongsTo(CustomerCategory::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(CustomerCategory::class, 'parent_id');
    }
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Customer')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
