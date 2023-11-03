<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Depertment extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_departments';

    protected $guarded = ['id'];

    /*
    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e', 'supervisors', 'attentions');
    }
    */
    
    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function depertmentTasks()
    {
        return $this->hasMany(DepertmentTask::class, 'department_id');
    }

    public function hasChildren()
    {
        $Children = $this->employees()->count() || $this->depertmentTasks()->count();
        return $Children;
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Depertment')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function getAttentionsAttribute($value)
    {
        return json_decode($value);
    }

    public function setAttentionsAttribute($value)
    {
        $this->attributes['attentions'] = json_encode($value);
    }

    public function getSupervisorsAttribute($value)
    {
        return json_decode($value);
    }

    public function setSupervisorsAttribute($value)
    {
        $this->attributes['supervisors'] = json_encode($value);
    }
}
