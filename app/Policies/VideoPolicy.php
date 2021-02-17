<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoFavorite;
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
    // if user is anonymous must $user is null by default
    public function like(User $user=null, Video $video)
    {
        if($video && $video->isAccepted($video)){
            $condition = [
                'video_id'=>$video->id,
                'user_id'=>$user ? $user->id: null
            ];
            if(empty($user)){
                $condition['user_ip'] = client_ip();
            }

            return VideoFavorite::query()->where($condition)->count() == 0;
        }

        return false;
    }

    public function unLike(User $user=null, Video $video)
    {
        if($video && $video->isAccepted()){
            $condition = [
                'video_id'=>$video->id,
                'user_id'=> $user ? $user->id : null
            ];

            if(empty($user)){
                $condition['user_ip']= client_ip();
            }

            return VideoFavorite::query()->where($condition)->count();
        }

        return false;
    }

    // if video::class pass in allows $video is null by default
    public function seeLikedVideos(User $user, Video $video=null)
    {
        return true;
    }

    public function delete(User $user, Video $video)
    {
        return $video->user_id === $user->id;
    }

    public function showStatistics(User $user, Video $video)
    {
        return $user->id === $video->user_id;
    }
}
