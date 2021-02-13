<?php

namespace App\Models;

use phpDocumentor\Reflection\Utils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    //region constant
    const STATE_PENDING = 'pending';
    const STATE_ACCEPTED = 'accepted';
    const STATE_READ = 'read';
    const STATES = [self::STATE_PENDING, self::STATE_ACCEPTED, self::STATE_READ];
    //endregion constant

    //region model config
    protected $table = 'comments';
    protected $fillable = ['user_id', 'video_id', 'parent_id', 'body','state'];
    //endregion model config

    //region relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    //endregion relation

    //region static method
    public static function channelCommand($userId)
    {
        return Comment::query()->join('videos', 'videos.id', '=', 'comments.video_id')
            ->selectRaw('comments.*')
            ->where('comments.user_id', $userId);
    }
    //endregion static method

    //region override method
//    public function getRouteKeyName()
//    {
//        return 'id';
//    }
    //endregion override method
}
