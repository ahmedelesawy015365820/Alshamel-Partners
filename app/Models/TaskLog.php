<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'action',
        "data",
        'message',
        'message_e',
        'task_id',
        'company_id',
        'user_id'
    ];
    protected $table = "general_task_logs";

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



    public function getDataAttribute($value)
    {
        return json_decode($value);
    }


    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }
}
