<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class VideoFavorite extends Pivot
{
    protected $table = 'video_favoites';
    protected $fillable = ['user_id', 'video_id'];
}
