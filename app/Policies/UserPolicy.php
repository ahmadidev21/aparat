<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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

    public function follow(User $user, User $otherUser)
    {
        return ($user->id != $otherUser->id) && (!$user->followings()->where('user_id2', $otherUser->id)->count());
    }

    public function unFollow(User $user, User $otherUser)
    {
        return ($user->id != $otherUser->id) && ($user->followings()->where('user_id2', $otherUser->id)->count());
    }
}
