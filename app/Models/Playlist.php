<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'playlists';

    protected $fillable =['user_id', 'title'];

    //region relation
    public function videos()
    {
        return $this->belongsToMany(Video::class, 'playlist_videos');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //endregion relation

    public function toArray()
    {
        $data = parent::toArray();
        $data['count'] = $this->videos()->count();

        return $data;
    }
}
