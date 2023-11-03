<?php

namespace App\Models;

use App\Traits\CompanyScopeTrait;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    use HasFactory, LogTrait   ;

    protected $table = 'general_role_user';

    protected $guarded = ['id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
