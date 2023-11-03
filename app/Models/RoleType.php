<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class RoleType extends Model
{
    use HasFactory, LogTrait, SoftDeletes   ;

    protected $table = 'general_role_types';

    protected $fillable = ['name',"name_e"];

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Role Type')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'roletype_id');
    }

    public function hasChildren()
    {
        $h = $this->roles()->count() > 0;
        return $h;
    }
}
