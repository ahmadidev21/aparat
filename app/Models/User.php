<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    //region types
    const TYPE_ADMIN = 'admin';
    const TYPE_USER = 'user';
    const TYPES = [self::TYPE_ADMIN, self::TYPE_USER];
   //endregion types

    //region model configs
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'mobile',
        'email',
        'name',
        'password',
        'avatar',
        'website',
        'verify_code',
        'verified_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'verify_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    //endregion model configs

    //region custom method
    /**
     * پیدا کردن یوزر از طریق موبایل یا ایمیل
     * @param $username
     *
     * @return mixed
     */
    public function findForPassport($username)
    {
        $user = static::where('mobile', $username)->orWhere('email', $username)->first();
        return $user;
    }

    public function isAdmin()
    {
        return $this->type === User::TYPE_ADMIN;
    }

    public function isUser()
    {
        return $this->type === User::TYPE_USER;
    }

    //endregion custom method

    //region setter
    public function setMobileAttribute($value)
    {
        $this->attributes['mobile'] = to_valid_mobile_number($value);
    }
    //endregion setter

    //region relation
    public function channel()
    {
        return $this->hasOne(Channel::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    public function channelVideos()
    {
        return $this->hasMany(Video::class)->selectRaw('*,0 as republished');
    }

    public function republishVideos()
    {
        return $this->hasManyThrough(Video::class,
            VideoRepublish::class,
            'user_id', 'id',
            'id',
            'video_id')->selectRaw('videos.*, 1 as republished');
    }

    public function videos()
    {
        return $this->channelVideos()->union($this->republishVideos());
    }

    public function favoriteVideos()
    {
        return $this->hasManyThrough(Video::class, VideoFavorite::class, 'user_id', 'id', 'id','video_id');
    }

    //endregion relation
}
