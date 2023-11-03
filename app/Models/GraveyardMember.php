<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\AllRequest;


class GraveyardMember extends Model
{
    use HasFactory;

    

    protected $table = "zulikhbat_members.sql";//'Members';
    protected $guarded = ['id'];

}
