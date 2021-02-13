<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use Psy\Command\Command;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
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

    public function changeState(User $user, Comment $comment, $state)
    {
        return (
            ($comment->state === Comment::STATE_PENDING) && ($state ===Comment::STATE_READ || $state === Comment::STATE_ACCEPTED)
                ||
            ($comment->state === Comment::STATE_READ && $state === Comment::STATE_ACCEPTED)
        ) && $user->channelVideos()->where('id', $comment->video_id)->count();
    }
}
