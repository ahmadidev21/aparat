<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

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
    //endregion relation

    //region override method
    public function getRouteKeyName()
    {
        return 'slug';
    }
    //endregion override method
}
