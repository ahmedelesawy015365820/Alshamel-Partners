<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'model_id',
        'model_type',
    ];
    protected $table = 'general_video_links';
    public function model()
    {
        return $this->morphTo();
    }

}
