<?php

namespace Modules\BoardsRent\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class CustomerSource extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];

    // protected $fillable = [
    //     'name',
    //     'name_e',
    //     "parent_id",
    // ];


    protected $table = "boards_rent_customer_sources";
    // relations

    // parent relation
    public function parent()
    {
        return $this->belongsTo(CustomerSource::class, 'parent_id');
    }

    // children relation

    public function children()
    {
        return $this->hasMany(CustomerSource::class, 'parent_id');
    }

    //  hasChildren
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('CustomerSource')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
