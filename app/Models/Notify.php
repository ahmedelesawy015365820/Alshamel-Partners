<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function role(){
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class,'notify-roles','notify_id','role_id');
    }
}
