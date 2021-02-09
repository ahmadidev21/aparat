<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoRepublish;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoPolicy
{
    use HandlesAuthorization;

    public function changeState(User $user, Video $video)
    {
        return $user->isAdmin();
    }

    public function republish(User $user, Video $video)
    {
        return $video
            && $video->isAccepted()
            && ($user->id != $video->user_id && VideoRepublish::where(['user_id'=>$user->id,'video_id'=>$video->id])->count() < 1);
    }

    public function like(User $user=null, Video $video)
    {
        return $video &&($video->isAccepted($video));
    }
}
