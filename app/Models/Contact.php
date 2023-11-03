<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_contacts';
    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e',
            'description', 'description_e', 'model_id',
            'model_type', 'socials', 'phones', 'job_id', 'priority_id')
            ->with('job:id,screen_id,property_id', 'priority:id,screen_id,property_id');
    }

    //  get set attributes

    public function getSocialsAttribute($value)
    {
        return json_decode($value);
    }

    public function setSocialsAttribute($value)
    {
        $this->attributes['socials'] = json_encode($value);
    }

    public function getPhonesAttribute($value)
    {
        return json_decode($value);
    }

    public function setPhonesAttribute($value)
    {
        $this->attributes['phones'] = json_encode($value);
    }

    //  relations

    public function job()
    {
        return $this->belongsTo(ScreenTreeProperty::class, 'job_id', 'id');
    }

    public function priority()
    {
        return $this->belongsTo(ScreenTreeProperty::class, 'priority_id', 'id');
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Contact')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
