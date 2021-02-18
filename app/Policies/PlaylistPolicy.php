<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;
use App\Models\Playlist;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlaylistPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function addVideo(User $user , Playlist $playlist , Video $video)
    {
        return $user->id === $playlist->user_id && $user->id === $video->user_id;
    }
}
