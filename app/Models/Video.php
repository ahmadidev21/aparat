<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    //region state
    const STATE_PENDING = 'pending';
    const STATE_CONVERTED = 'converted';
    const STATE_ACCEPTED = 'accepted';
    const STATE_BLOCKED = 'blocked';
    const STATES = [self::STATE_PENDING, self::STATE_CONVERTED, self::STATE_ACCEPTED, self::STATE_BLOCKED];
    //endregion state

    //region custom method
    public function isAccepted()
    {
        return $this->state === self::STATE_ACCEPTED;
    }

    public function isPengind()
    {
        return $this->state === self::STATE_PENDING;
    }

    public function isConverted()
    {
        return $this->state === self::STATE_CONVERTED;
    }

    public function isBlocked()
    {
        return $this->state === self::STATE_BLOCKED;
    }


    //endregion custom method

    //region model config
    protected $table = 'videos';

    protected $fillable = [
        'user_id',
        'category_id',
        'channel_category_id',
        'slug',
        'title',
        'info',
        'duration',
        'banner',
        'enable_comments',
        'publish_at',
        'state'
    ];
    //endregion model config

    //region relation
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'video_tags');
    }

    public function playlist()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_videos');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'video_views')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    //endregion relation

    //region override method
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function toArray()
    {
        $data = parent::toArray();
        $condition = [
            'video_id'=>$this->id,
            'user_id'=> auth('api')->check() ? auth('api')->id() : null
        ];
        if(!auth('api')->check()){
            $condition['user_ip'] = client_ip();
        }
        $data['like'] = VideoFavorite::query()->where($condition)->count();
        $data['tags'] = $this->tags;

        return $data;
    }
    //endregion override method

    //region custom static method
    public static function whereNotRepublished()
    {
        return Video::whereRaw('id not in(select video_id from video_republishes)');
    }

    public static function whereRepublished()
    {
        return Video::whereRaw('id in(select video_id from video_republishes)');
    }

    public static function views($userId){
        //TODO: anonymous user view
        return static::query()->where('videos.user_id',$userId)
            ->join('video_views', 'video_views.video_id', '=','videos.id');
    }

    public static function channelComments($userId){

        return static::query()->join('comments', 'comments.video_id', '=','videos.id')
            ->where('videos.user_id',$userId);
    }
    //endregion custom static method
}
