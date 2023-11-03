<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class Role extends Model
{
    use HasFactory,SoftDeletes, LogTrait   ;
    protected $table = 'general_roles';

    protected $guarded = ['id'];

    public function roleType()
    {
        return $this->belongsTo(RoleType::class,'roletype_id','id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'general_role_user', 'role_id', 'user_id');
    }

    public function workflows()
    {
        return $this->hasMany(RoleWorkflow::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Role')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function hasChildren(){
        if ($this->users ()){
            return true;
        }
        return false;
    }
}
